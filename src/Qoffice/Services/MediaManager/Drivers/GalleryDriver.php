<?php

namespace Qoffice\Services\MediaManager\Drivers;

use Laravel\URL;
use Laravel\Auth;
use Qoffice\Models\Gallery;

/**
* Gallery class
*
* @package default
* @author ilpaijin <ilpaijin@gmail.com>
*/
class GalleryDriver extends BaseDriver implements DriverInterface
{	
	/**
	 * [$keysAllowed description]
	 * @var array
	 */
	protected $keysAllowed = array('gallery_image');

	/**
	 * [$path description]
	 * @var string
	 */
	protected $path = 'gallery';

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
		if(! Gallery::where('idRef', '=', $idutente)->where('filename', '=', $file)->delete()) return false;
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
		$doc = Gallery::insert( array(
		    'idRef' => $this->input['idutente'],
		    'operator' => $user->get_id(),
		    'pathname' => '/qoffice/media/gallery/'.$this->input['idutente'].'/'.$filename,
		    'filename' => $filename,
		    'codice' => $fileKey
		));
	}
}