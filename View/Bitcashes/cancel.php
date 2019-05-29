<?php $this->BcBaser->css(array('Point.point'), array('inline' => false)); ?>
<?php echo $this->Session->flash(); ?>

<h1 class="h5 border-bottom py-3 mb-3 text-secondary">ビットキャッシュ決済キャンセル</h1>
<div class="my-3 mx-sm-5">
	<p>ビットキャッシュ決済が途中で取り消し（キャンセル）されました。</p>
	<p>やり直す場合は「ポイント購入」へ戻り、再度お手続きお願いいたします。</p>
</div>
<div class="mt-3 mb-5 mx-sm-5">
	<?php echo $this->BcBaser->link( 'ポイント購入へ戻る', '/point/point_users/payselect', ['class'=>'btn btn-outline-primary btn-e']);?>
</div>

<div class="my-4 mx-sm-5">
	<p>ひらがなIDの残高を確認する。</p>
	<?php echo $this->BcBaser->link('<i class="fas fa-external-link-alt"></i>ひらがなIDの残高照会', 'https://bitcash.jp/bitcash/balance', ['class'=>'btn btn-outline-secondary btn-e', 'target'=>'_blank']);?>
</div>
<div class="my-4 mx-sm-5">
	<p>ひらがなIDの残高を、別のひらがなIDへ引継ぐ。</p>
	<?php echo $this->BcBaser->link('<i class="fas fa-external-link-alt"></i>ひらがなIDの残高引継', 'https://bitcash.jp/bitcash/merge', ['class'=>'btn btn-outline-secondary btn-e', 'target'=>'_blank']);?>
</div>