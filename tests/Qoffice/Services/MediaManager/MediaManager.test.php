<?php
require_once 'vendor/autoload.php';
/**
* TestMediamanager class
*
* @package default
* @author ilpaijin <ilpaijin@gmail.com>
*/
class TestMediamanager extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		// Bundle::start('qoffice');
		$this->mm = new Qoffice\Services\MediaManager\MediaManager('assegni');
	}

	public function testItHasTheRightInstance()
	{
		$this->mm = new Qoffice\Services\MediaManager\MediaManager('assegni');

		$this->assertInstanceOf('Qoffice\Services\MediaManager\MediaManager',$this->mm, 'message');
	}

	public function testDriverAttributeIsNotEmptyOnConstruction()
	{
		$this->mm = new Qoffice\Services\MediaManager\MediaManager('assegni');

		$this->assertAttributeNotEmpty('driver', $this->mm);
	}

	public function testDriverAtributeHasTheRightInstance()
	{
		$this->mm = new Qoffice\Services\MediaManager\MediaManager('assegni');

		$this->assertInstanceOf('Qoffice\\Services\\Mediamanager\\Drivers\\AssegniDriver', $this->mm->getDriver());
	}

	/**
     * @expectedException InvalidArgumentException
     */
	public function testItTrowsAnExceptionIfTheDriverIsNotCorrectOrEmptyForPOSTRequest()
	{
		$this->mm = new Qoffice\Services\MediaManager\MediaManager();
	}

	public function testDriverIsCalledOnManagerfunctionCall()
	{
		$mockDriver = $this->getMock('Qoffice\\Services\\Mediamanager\\Drivers\\AssegniDriver');
		$this->mm->setDriver($mockDriver);
		
		$mockDriver->expects($this->once())
			->method('uploadFile');

		$this->mm->uploadMedia();	
	}
}