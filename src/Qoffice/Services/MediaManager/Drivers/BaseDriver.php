<?php

namespace Qoffice\Services\MediaManager\Drivers;

use Laravel\File;
use Laravel\Input;
use Laravel\Config;
use Laravel\Validator;

/**
* BaseDriver class
*
* @package default
* @author ilpaijin <ilpaijin@gmail.com>
*/
abstract class BaseDriver 
{
	/**
	 * [$input description]
	 * @var [type]
	 */
	protected $input;

	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		$this->input = Input::all();
	}

	/**
	 * [deleteFile description]
	 * @param  [type] $idutente [description]
	 * @param  [type] $file     [description]
	 * @return [type]           [description]
	 */
	public function deleteFile($idutente, $file)
	{
		if(!file_exists(path('storage').$this->path.DS.$idutente.DS.$file))
			return false;

		if (!File::delete(path('storage').$this->path.DS.$idutente.DS.$file))
			return false;

		$this->removeFromDb($idutente, $file);
	}

	/**
	 * [isValid description]
	 * @param  [type]  $fileKey [description]
	 * @return boolean          [description]
	 */
	protected function isValid($fileKey)
	{
		$rules = array(	$fileKey => $this->mimes );
		$validation = Validator::make($this->input[$fileKey], $rules);

		if($validation->fails())
		{
			return $validation->errors;
		}

		return in_array($fileKey, $this->keysAllowed);
	}

	/**
	 * [getDestinationPath description]
	 * @return [type] [description]
	 */
	protected function getDestinationPath()
	{
		$storagepath = path('storage').$this->path.DS.$this->input['idutente'];
		if (! File::exists($storagepath))
		{
			File::mkdir($storagepath);
		}
		return $storagepath.DS;
	}

	/**
	 * [getFilename description]
	 * @param  [type] $destinationPath [description]
	 * @param  [type] $filename        [description]
	 * @return [type]                  [description]
	 */
	protected function getFilename($destinationPath, $filename)
	{
		$ext = File::extension($filename);
        $rawFilename = str_replace($ext, '', $filename);
        $rawFilename = str_replace(array(' '), '_', $rawFilename);

		$filecount = glob($destinationPath . $rawFilename."*");
		$filecount = count($filecount);

		return $rawFilename.'-'.($filecount+1).'.'.$ext;
	}

	/**
	 * [buildResource description]
	 * @param  [type] $fileKey [description]
	 * @return [type]          [description]
	 */
	protected function buildResource($fileKey)
	{
		$aFile = array();
		foreach( $this->input[$fileKey] as $sProp => $aProp )
		{
			foreach( $aProp as $intkey => $prop )
			{
				if( ($sProp == 'error') && ($prop !== 0) ) return false;

				$aFile[$sProp] = $prop;
			}
		}
		return $aFile;
	}
}