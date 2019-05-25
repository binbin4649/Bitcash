<?php $this->BcBaser->css(array('Point.point'), array('inline' => false)); ?>
<?php echo $this->Session->flash(); ?>

<h1 class="h5 border-bottom py-3 mb-3 text-secondary">ビットキャッシュ決済キャンセル</h1>
<div class="my-3 mx-sm-5 text-center">
	<p>ビットキャッシュ決済が途中で取り消し（キャンセル）されました。</p>
	<p>やり直す場合は「ポイント購入」へ戻り、再度お手続きお願いいたします。</p>
</div>
<div class="my-3 mx-sm-5">
	<?php echo $this->BcBaser->link( 'ポイント購入へ戻る', '/point/point_users/payselect', ['class'=>'btn btn-outline-primary btn-e']);?>
</div>