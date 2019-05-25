<?php

class BitcashFixture extends CakeTestFixture {
	
	public $useDbConfig = 'test';
	public $import = array('model' => 'Bitcash.Bitcash');
	
	public $records = array(
		array(
			'id' => 1,
			'mypage_id' => 2,
			'point_book_id' => null,
			'point_user_id' => null,
			'tran_id' => 'test',
			'status' => 'before',
			'amount' => 1500,
			'created' => '2018-07-30 14:06:01',
			'modified' => '2018-07-30 14:06:01'
		),
		array(
			'id' => 3,
			'mypage_id' => 3,
			'point_book_id' => null,
			'point_user_id' => null,
			'tran_id' => 'test3',
			'status' => 'SUCCESS',
			'amount' => 1500,
			'created' => '2018-07-30 14:06:01',
			'modified' => '2018-07-30 14:06:01'
		)
	);

}