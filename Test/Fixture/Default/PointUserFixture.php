<?php

class PointUserFixture extends CakeTestFixture {
	
	public $useDbConfig = 'test';
	public $import = array('model' => 'Point.PointUser');
	
	public $records = array(
		array(
			'id' => 1,
			'mypage_id' => 1,
			'point' => 100,
			'credit' => 0,
			'available_point' => 100,
			'pay_plan' => 'basic',
			'created' => '2018-07-30 16:26:01',
			'modified' => '2018-07-30 16:26:01',
		),
	);

}