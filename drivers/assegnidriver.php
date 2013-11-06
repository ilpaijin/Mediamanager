<?php

namespace Qoffice\Services\Mediamanager\Drivers;

use Laravel\URL;
use Laravel\Auth;
use Qoffice\Models\Assegni;

/**
* AssegniUploader class
*
* @package default
* @author ilpaijin <ilpaijin@gmail.com>
*/
class AssegniDriver extends BaseDriver implements DriverInterface
{	
	/**
	 * [$keysAllowed description]
	 * @var array
	 */
	protected $keysAllowed = array('assegni_image');

	/**
	 * [$path description]
	 * @var string
	 */
	protected $path = 'assegni';
	
	/**
	 * [$mimes description]
	 * @var string
	 */
	protected $mimes = 'mimes:jpg,jpeg,png,bmp,gif';

	/**
	 * [$persist description]
	 * @var boolean
	 */
	protected $persist = true;

	/**
	 * [uploadFile description]
	 * @return [type] [description]
	 */
	public function uploadFile()
	{
		$fileKey = key($this->input);

		if(!$this->isValid($fileKey))
		{
			return false;
		}

		$aFile = $this->buildResource($fileKey);

		if (is_uploaded_file($aFile['tmp_name']))
		{
            $destinationPath = $this->getDestinationPath();

            $filename = $this->getFilename($destinationPath, $aFile['name']);

            if (!move_uploaded_file($aFile['tmp_name'], $destinationPath.$filename)) 
            {
                $error = error_get_last();
                throw new \Exception(sprintf('Could not move the file "%s" to "%s" (%s)', $aFile['tmp_name'], $filename, strip_tags($error['message'])));
            }

            chmod($destinationPath.$filename, 0666 & ~umask());

            $aFile['pathname'] = URL::base().'/qoffice/media/'.$this->path.'/'.$this->input['idutente'].'/'.$filename;
		    $aFile['delete_url'] = URL::base().'/qoffice/accounts/deleteMedia/'.$this->path.'/'.$this->input['idutente'].'/'.$filename;
		    $aFile['delete_type'] = 'DELETE';

		    if($this->persist)
		    	$this->saveToDb($filename, $fileKey);

            return json_encode( array($aFile) );
		}
	}

	/**
	 * [removeFromDb description]
	 * @return [type] [description]
	 */
	public function removeFromDb($idutente, $file)
	{
		if(! Assegni::where('idRef', '=', $idutente)->where('filename', '=', $file)->delete()) return false;
	}

	/**
	 * [saveToDb description]
	 * @param  [type] $filename [description]
	 * @param  [type] $fileKey  [description]
	 * @return [type]           [description]
	 */
	public function saveToDb($filename, $fileKey)
	{
		$user = Auth::user();
		$doc = Assegni::insert( array(
		    'idRef' => $this->input['idutente'],
		    'operatore' => $user->get_id(),
		    'pathname' => '/qoffice/media/assegni/'.$this->input['idutente'].'/'.$filename,
		    'filename' => $filename,
		    'codice' => $fileKey,
		    'stato' => $this->input['stato'],
		    'importo' => $this->input['importo']
		));
	}
}