<?php 
class BitcashesSchema extends CakeSchema {

	public $file = 'bitcashes.php';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $bitcashes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'mypage_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'point_book_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'point_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'tran_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'status' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'amount' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

}
