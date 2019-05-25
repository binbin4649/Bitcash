<?php

App::import('Model', 'AppModel');
App::uses('CakeEmail', 'Network/Email');

class Bitcash extends AppModel {

	public $name = 'Bitcash';
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->PointUser = ClassRegistry::init('Point.PointUser');
		$this->Mypage = ClassRegistry::init('Members.Mypage');
	}
	
	//新しいレコードを作る時、beforeがあったら、deleteにしてから新しく作る
	public function newSave($data){
		if(empty($data['Bitcash']['mypage_id'])){
			return false;
		}
		//新規登録の初期値
		$data['Bitcash']['status'] = 'before';
		//前にあるか調べる
		$before = $this->find('first',array(
		  'conditions'=>array(
		  	'Bitcash.mypage_id' => $data['Bitcash']['mypage_id'],
		  	'Bitcash.status' => 'before'
		  	)
		));
		if(empty($before)){
			//新規登録
			$this->create();
			if($this->save($data)){
				return $data;
			}else{
				$this->log('Bitcash.php newSave. new data save error. : '.print_r($before, true));
				return false;
			}
		}else{
			// status=delete してから新規登録
			$before['Bitcash']['status'] = 'delete';
			$this->create();
			if($this->save($before)){
				$this->create();
				if($this->save($data)){
					return $data;
				}else{
					$this->log('Bitcash.php newSave. data save error. : '.print_r($before, true));
					return false;
				}
			}else{
				$this->log('Bitcash.php newSave. before delete save error. : '.print_r($before, true));
				return false;
			}
		}
	}
	
	//cancelされたら、deleteにする
	public function beforeCancel($mypage_id){
		if(empty($mypage_id)){
			return false;
		}
		$before = $this->find('first',array(
		  'conditions'=>array(
		  	'Bitcash.mypage_id' => $mypage_id,
		  	'Bitcash.status' => 'before'
		  	)
		));
		if($before){
			$before['Bitcash']['status'] = 'delete';
			$this->create();
			if($this->save($before)){
				return true;
			}else{
				$this->log('Bitcash.php beforeCancel. save error. : '.print_r($before, true));
				return false;
			}
		}else{
			return false;
		}
	}
	
	
	public function charge($mypage_id, $select_amount){
		$siteUrl = Configure::read('BcEnv.siteUrl');
		$shop_id = Configure::read('Bitcash.ShopId');
		$shop_key = Configure::read('Bitcash.ShopKey');
		if(empty($mypage_id) or empty($select_amount) or empty($siteUrl) or empty($shop_id) or empty($shop_key)){
			$this->log('Bitcash.php charge empty error.');
			return false;
		}
		$param = array(
		    'shop_id' => $shop_id,
		    'shop_key' => $shop_key,
			'price' => $select_amount,
			'rating' => '19',
			'order_id' => $mypage_id,
			//'notify_url' => $siteUrl.'bitcash/bitcashes/thanks',
			'notify_url' => 'none',
			'return_url' => $siteUrl.'bitcash/bitcashes/thanks',
			'cancel_url' => $siteUrl.'bitcash/bitcashes/cancel'
		);
		$param = http_build_query($param, '', '&');
		$header = array(
			'Content-Type: application/x-www-form-urlencoded',
			'Content-Length: '.strlen($param)
		);
		$options = array('http' => array(
			'method' => 'POST',
			'header' => implode("\r\n", $header),
			'content' => $param
		));
		$prepare_settle_url = 'https://settle.bitcash.co.jp/settle/tri?act=prepareSettle';
		$contents = file_get_contents($prepare_settle_url, false, stream_context_create($options));
		
		$responceParam = explode('&', $contents);
		for ($paramCount = 0; $paramCount < count($responceParam); $paramCount++) {
			// レスポンスパラメータをさらに"="で分割する
			$responceParams = explode('=', $responceParam[$paramCount]);
			// 左辺を配列キーに、右辺を値に格納する
			$map[$responceParams[0]] = $responceParams[1];
		}
		if($map['status_string'] == 'SUCCESS'){
			$Bitcash['Bitcash'] = [
				'mypage_id' => $mypage_id,
				'tran_id' => $map['tran_id'],
				'amount' => $select_amount
			];
			if(!$this->newSave($Bitcash)){
				$this->log('Bitcash.php charge. newSave error. : '.print_r($Bitcash, true));
				$map['status_string'] = 'charge_save_error';
				$map['status_code'] = '950';
			}
		}
		return $map;
	}
	
	public function confirmPay($mypage_id){
		// status=before が無ければエラー
		$Bitcash = $this->find('first',array(
		  'conditions'=>array(
		  	'Bitcash.mypage_id' => $mypage_id,
		  	'Bitcash.status' => 'before'
		  	)
		));
		if(empty($Bitcash)){
			return false;
		}
		$shop_id = Configure::read('Bitcash.ShopId');
		$shop_key = Configure::read('Bitcash.ShopKey');
		$confirmUrl = 'https://settle.bitcash.co.jp/settle/tri?act=confirmSettle';
		$param = array(
		    'shop_id' => $shop_id,
		    'shop_key' => $shop_key,
			'tran_id' => $Bitcash['Bitcash']['tran_id']
		);
		$param = http_build_query($param, '', '&');
		$header = array(
				'Content-Type: application/x-www-form-urlencoded',
				'Content-Length: '.strlen($param)
		);
		$options = array('http' => array(
		    'method' => 'POST',
			'header' => implode("\r\n", $header),
		    'content' => $param
		));
		$contents = file_get_contents($confirmUrl, false, stream_context_create($options));
		$responceParam = explode('&', $contents);
		for ($paramCount = 0; $paramCount < count($responceParam); $paramCount++) {
			// レスポンスパラメータをさらに"="で分割する
			$responceParams = explode('=', $responceParam[$paramCount]);
			// 左辺を配列キーに、右辺を値に格納する
			$map[$responceParams[0]] = $responceParams[1];
		}
		if($map['status_string'] == 'SUCCESS'){
			return $this->confirmPay2($mypage_id, $map, $Bitcash);
		}else{
			return $map;
		}
	}
	
	// SUCCESS時のこちら側の処理。テストするために分けた。
	public function confirmPay2($mypage_id, $map, $Bitcash){
		$price = $map['price'];
		//ポイント計算。購入金額によってはボーナスポイントがある
		$grant_point = '';
		$AmountList = Configure::read('PointPlugin.AmountList');
		foreach($AmountList as $amount=>$point){
			if($amount == $price){
				$grant_point = $point;
			}
		}
		if(empty($grant_point)){
			$grant_point = $price;
		}
		//ポイント入れる。
		$data = [
			'mypage_id' => $mypage_id,
			'point' => $grant_point,
			'reason' => 'bitcash',
			'pay_token' => $Bitcash['Bitcash']['tran_id'],
			'charge' => $price
		];
		$PointBook = $this->PointUser->pointAdd($data);
		if(!$PointBook){
			$this->log('Bitcash.php confirm. pointAdd error. : '.print_r($PointBook, true));
			return false;
		}
		// Bitcash更新
		$Bitcash['Bitcash']['status'] = 'SUCCESS';
		$Bitcash['Bitcash']['point_book_id'] = $PointBook['PointBook']['id'];
		$Bitcash['Bitcash']['point_user_id'] = $PointBook['PointBook']['point_user_id'];
		$this->create();
		if(!$this->save($Bitcash)){
			$this->log('Bitcash.php confirm. Bitcash save error. : '.print_r($Bitcash, true));
			return false;
		}
		//メール送信
		$PointBook['Mypage'] = $this->Mypage->findById($mypage_id)['Mypage'];
		$title = 'ビットキャッシュお支払い';
		if(!$this->sendEmail($PointBook['Mypage']['email'], $title, $PointBook, array('template'=>'Bitcash.charge', 'layout'=>'default'))){
			$this->log('Bitcash.php confirmPay2. sendMail error. : '.print_r($PointBook, true));
			return false;
		}
		return $PointBook;
	}
	
	// 使ってない。post通信の取得が上手くできなかった
	public function accept($post_data){
		$NotifyKey = Configure::read('Bitcash.NotifyKey');
		if(empty($post_data) or empty($NotifyKey)){
			$this->log('Bitcash.php accept. empty error.');
			return false;
		}
		$tranId = $post_data['tran_id'];
		$orderId = $post_data['order_id'];
		$settleDate = $post_data['settle_date'];
		$price = $post_data['price'];
		$hash = $post_data['hash'];
		// hash値確認
		$hashSource = '';
		$hashSource .= 'tran_id=';
		$hashSource .= urlencode(mb_convert_encoding($tranId, 'UTF-8', 'auto'));
		$hashSource .= '&order_id=';
		$hashSource .= urlencode(mb_convert_encoding($orderId, 'UTF-8', 'auto'));
		$hashSource .= '&settle_date=';
		$hashSource .= urlencode(mb_convert_encoding($settleDate, 'UTF-8', 'auto'));
		$hashSource .= '&price=';
		$hashSource .= urlencode(mb_convert_encoding($price, 'UTF-8', 'auto'));
		$hashSource .= $NotifyKey;
		$hashSource = sha1($hashSource);
		if($hash != $hashSource){
			$this->log('Bitcash.php accept. hash incorrect. : '.print_r($post_data, true));
			return false;
		}
		//ポイント計算。購入金額によってはボーナスポイントがある
		$grant_point = '';
		$AmountList = Configure::read('PointPlugin.AmountList');
		foreach($AmountList as $amount=>$point){
			if($amount == $price){
				$grant_point = $point;
			}
		}
		if(empty($grant_point)){
			$grant_point = $price;
		}
		//ポイント入れる。
		$data = [
			'mypage_id' => $orderId,
			'point' => $grant_point,
			'reason' => 'bitcash',
			'pay_token' => $tranId,
			'charge' => $price
		];
		$PointBook = $this->PointUser->pointAdd($data);
		if(!$PointBook){
			$this->log('Bitcash.php accept. pointAdd error. : '.print_r($PointBook, true));
			return false;
		}
		//メール送信
		$PointBook['Mypage'] = $this->Mypage->findById($orderId)['Mypage'];
		$title = 'ビットキャッシュお支払い';
		if(!$this->sendEmail($PointBook['Mypage']['email'], $title, $PointBook, array('template'=>'Bitcash.charge', 'layout'=>'default'))){
			$this->log('Bitcash.php accept. sendMail error. : '.print_r($PointBook, true));
			return false;
		}
		return $PointBook;
	}
	
	
	public function sendEmail($to, $title = '', $body = '', $options = array()){
		if(Configure::read('MccPlugin.TEST_MODE')){
			$email_piece = Configure::read('MccPlugin.TEST_EMAIL_PIECE');
			if(strpos($to, $email_piece) === false) return true;
		}
		if(!Configure::read('MccPlugin.TEST_MODE')){
			$bcc = Configure::read('MccPlugin.sendMailBcc');
			if(empty($bcc)){
				$bcc = Configure::read('BcSite.email');
			}
		}
		$this->siteConfigs = Configure::read('BcSite');
		$config = array(
			'transport' => 'Smtp',
			'host' => $this->siteConfigs['smtp_host'],
			'port' => ($this->siteConfigs['smtp_port']) ? $this->siteConfigs['smtp_port'] : 25,
			'username' => ($this->siteConfigs['smtp_user']) ? $this->siteConfigs['smtp_user'] : null,
			'password' => ($this->siteConfigs['smtp_password']) ? $this->siteConfigs['smtp_password'] : null,
			'tls' => $this->siteConfigs['smtp_tls'] && ($this->siteConfigs['smtp_tls'] == 1)
		);
		$cakeEmail = new CakeEmail($config);
		// charset
		if (!empty($this->siteConfigs['mail_encode'])) {
			$encode = $this->siteConfigs['mail_encode'];
		} else {
			$encode = 'ISO-2022-JP';
		}
		$cakeEmail->headerCharset($encode);
		$cakeEmail->charset($encode);
		$cakeEmail->emailFormat('text');
		
		$cakeEmail->addTo($to);
		$cakeEmail->subject($title);
		if (!empty($this->siteConfigs['formal_name'])) {
			$fromName = $this->siteConfigs['formal_name'];
		}else{
			$fromName = Configure::read('BcApp.title');
		}
		$from = $this->siteConfigs['email'];
		$body['mailConfig']['site_name'] = $fromName;
		$body['mailConfig']['site_url'] = Configure::read('BcEnv.siteUrl');
		$body['mailConfig']['site_email'] = $from;
		
		$cakeEmail->from($from, $fromName);
		if(!empty($bcc)){
			$cakeEmail->bcc($bcc);
		}
		$cakeEmail->replyTo($from);
		$cakeEmail->returnPath($from);
		$cakeEmail->viewRender('BcApp');
		if(empty($options['layout'])){
			$options['layout'] = 'default';
		}
		
		$cakeEmail->template($options['template'], $options['layout']);
		$cakeEmail->viewVars($body);
		
		try {
			$cakeEmail->send();
			return true;
		}catch(Exception $e){
			$this->log('PointUser.php sendEmail error. '.$e->getMessage());
			return false;
		}
	}

}
