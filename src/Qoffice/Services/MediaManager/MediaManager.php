<?php

namespace Qoffice\Services\MediaManager;

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
	 * [__construct description]
	 */
	public function __construct($type = null)
	{
		$inputType = $type ?: Input::get('type');
		$driverns = 'Qoffice\\Services\\MediaManager\\Drivers\\'. ucfirst($inputType).'Driver';

		if(!class_exists($driverns))
		{
			throw new \InvalidArgumentException('Driver not found');
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
	public function setDriver(Drivers\DriverInterface $driver)
	{
		$this->driver = $driver;
	}

	/**
	 * [getDriver description]
	 * @return [type] [description]
	 */
	public function getDriver()
	{
		return $this->driver;
	}
}