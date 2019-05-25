<?php 

class BitcashesController extends BitcashAppController {
  
  public $name = 'Bitcashes';

  public $uses = array('Plugin', 'Point.PointUser', 'Bitcash.Bitcash');
  
  public $helpers = array('BcPage', 'BcHtml', 'BcTime', 'BcForm');
  
  public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];
  
  public $subMenuElements = array('');

  public $crumbs = array(
    array('name' => 'マイページトップ', 'url' => array('plugin' => 'members', 'controller' => 'mypages', 'action' => 'index')),
  );

  public function beforeFilter() {
    parent::beforeFilter();
    $this->BcAuth->allow('thanks');
    if(preg_match('/^admin_/', $this->action)){
	   $this->subMenuElements = array('bitcash');
    }
    $this->Security->unlockedActions = array('thanks');
  }
  
  
  // bitcash 決済画面
  public function setle($amount){
	  $this->autoRender = false;
	  $user = $this->BcAuth->user();
	  $amountList = Configure::read('PointPlugin.AmountList');
      if(!$user){
		$this->setMessage('エラー: user error.', true);
		$this->redirect(array('plugin'=>'members', 'controller'=>'mypages', 'action'=>'index'));
	  }
	  $map = $this->Bitcash->charge($user['id'], $amount);
	  if($map['status_string'] == 'SUCCESS'){
		  $this->redirect(urldecode($map['settle_url']));
	  }elseif($map['status_string'] == 'MAINTENANCE'){
		  $error_code = $map['status_code'].':'.$map['status_string'];
		  $this->setMessage('メンテナンス中、少し時間をあけて再度お試しください。'.$error_code, true);
		  $this->redirect(array('plugin'=>'point', 'controller'=>'point_users', 'action'=>'payselect'));
	  }else{
		  $error_code = $map['status_code'].':'.$map['status_string'];
		  $this->setMessage('エラー：本メッセージをコピーして、ご連絡お願いいたします。'.$error_code, true);
		  $this->redirect(array('plugin'=>'point', 'controller'=>'point_users', 'action'=>'payselect'));
	  }
  }
  
  public function thanks(){
	  $user = $this->BcAuth->user();
      if(!$user){
		$this->setMessage('エラー: user error.', true);
		$this->redirect(array('plugin'=>'members', 'controller'=>'mypages', 'action'=>'index'));
	  }
	  $PointBook = $this->Bitcash->confirmPay($user['id']);
	  if(!$PointBook){
		  $this->setMessage('エラー: 実行中の決済がありません。', true);
		  $this->redirect(array('plugin'=>'point', 'controller'=>'point_users', 'action'=>'payselect'));
	  }
	  if(!empty($PointBook['status_string'])){
		  $error_code = $map['status_code'].':'.$map['status_string'];
		  $this->setMessage('エラー：本メッセージをコピーして、ご連絡お願いいたします。'.$error_code, true);
		  $this->redirect(array('plugin'=>'point', 'controller'=>'point_users', 'action'=>'payselect'));
	  }
	  $this->set('book', $PointBook);
  }
  
  public function cancel(){
	  
  }



}






?>