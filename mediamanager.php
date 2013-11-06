<?php

namespace Qoffice\Services\Mediamanager;

use Laravel\Input;

/**
* Mediamanager class
*
* @package default
* @author ilpaijin <ilpaijin@gmail.com>
*/
class Mediamanager 
{
	/**
	 * [$uploader description]
	 * @var [type]
	 */
	protected $driver;

	/**
	 * [$input description]
	 * @var [type]
	 */
	protected $input;

	/**
	 * [$config description]
	 * @var [type]
	 */
	protected $config;

	/**
	 * [$response description]
	 * @var [type]
	 */
	public $response;

	/**
	 * [__construct description]
	 */
	public function __construct($type = null)
	{
		$inputType = $type ?: Input::get('type');
		$driverns = 'Qoffice\\Services\\Mediamanager\\Drivers\\'. ucfirst($inputType).'Driver';

		if(!class_exists($driverns))
		{
			throw new \Exception('Driver not found');
		}

		$this->setDriver(new $driverns());
	}

	/**
	 * [uploadMedia description]
	 * @return [type] [description]
	 */
	public function uploadMedia()
	{
		return $this->driver->uploadFile();
	}

	/**
	 * [deleteMedia description]
	 * @param  [type] $idutente [description]
	 * @param  [type] $file     [description]
	 * @return [type]           [description]
	 */
	public function deleteMedia($idutente, $file)
	{
		return $this->driver->deleteFile($idutente, $file);
	}

	/**
	 * [setTpye description]
	 * @param DriversDriverInterface $type [description]
	 */
	private function setDriver(Drivers\DriverInterface $driver)
	{
		$this->driver = $driver;
	}
}