<?php
App::uses('Bitcash', 'Bitcash.Model');

class BitcashTest extends BaserTestCase {
	
    public $fixtures = array(
        'plugin.bitcash.Default/PointUser',
        'plugin.bitcash.Default/PointBook',
        'plugin.bitcash.Default/Mypage',
        'plugin.bitcash.Default/Mylog',
        'plugin.bitcash.Default/Bitcash'
    );

    public function setUp() {
	    Configure::write('MccPlugin.TEST_MODE', true);
        $this->Bitcash = ClassRegistry::init('Bitcash.Bitcash');
        parent::setUp();
    }
    
    public function tearDown(){
	    unset($this->Bitcash);
	    parent::tearDown();
    }

    public function testNewSaveFirstSucsses(){
	    $data['Bitcash']['mypage_id'] = 1;
	    $data['Bitcash']['tran_id'] = 'testtest';
	    $data['Bitcash']['amount'] = 1500;
	    $r = $this->Bitcash->newSave($data);
	    $this->assertEquals('testtest', $r['Bitcash']['tran_id']);
    }
    
    public function testNewSaveSecondSucsses(){
	    $data['Bitcash']['mypage_id'] = 2;
	    $data['Bitcash']['tran_id'] = 'test2';
	    $data['Bitcash']['amount'] = 1500;
	    if($this->Bitcash->newSave($data)){
		    $r = $this->Bitcash->findById(1);
	    }
	    $this->assertEquals('delete', $r['Bitcash']['status']);
    }
    
    public function testBeforeCancelTrue(){
	    $r = $this->Bitcash->beforeCancel(2);
	    $this->assertTrue($r);
    }
    
    public function testBeforeCancelFalse(){
	    $r = $this->Bitcash->beforeCancel(3);
	    $this->assertFalse($r);
    }
    
    public function testCharge(){
	    $mypage_id = 1;
	    $select_amount = 1000;
	    $r = $this->Bitcash->charge($mypage_id, $select_amount);
	    $this->assertEquals('SUCCESS', $r['status_string']);
    }
    
    public function testConfirmPay(){
	    $mypage_id = 2;
	    $r = $this->Bitcash->confirmPay($mypage_id);
	    $this->assertEquals('INVALID_TRAN_ID', $r['status_string']);
    }
    
    public function testConfirmPay2(){
	    $mypage_id = 1;
	    $map['price'] = 1500;
	    $Bitcash['Bitcash']['tran_id'] = 'testtest';
	    $Bitcash['Bitcash']['mypage_id'] = '1';
	    $Bitcash['Bitcash']['amount'] = '1500';
	    $r = $this->Bitcash->confirmPay2($mypage_id, $map, $Bitcash);
	    $this->assertEquals('bitcash', $r['PointBook']['reason']);
    }
    
    
    

}