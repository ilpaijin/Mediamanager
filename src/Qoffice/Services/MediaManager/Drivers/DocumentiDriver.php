<?php

namespace Qoffice\Services\MediaManager\Drivers;

use Laravel\URL;
use Laravel\Auth;
use Qoffice\Models\Doqu;

/**
* DocumentiUploader class
*
* @package default
* @author ilpaijin <ilpaijin@gmail.com>
*/
class DocumentiDriver extends BaseDriver implements DriverInterface
{	
	/**
	 * [$keysAllowed description]
	 * @var array
	 */
	protected $keysAllowed = array('FN','CED','CI','VP','CCIAA','AA','ALL9','RACC','CAS','CAR','IVA');

	/**
	 * [$path description]
	 * @var string
	 */
	protected $path = 'docs';

	/**
	 * [$mimes description]
	 * @var string
	 */
	protected $mimes = 'mimes:docx,jpg,jpeg,png,bmp,gif,doc,pdf,xls';

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
		if(!Doqu::where('idRef', '=', $idutente)->where('nomefile', '=', $file)->delete()) return false;
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
		$doc = Doqu::insert( array(
		    'idRef' => $this->input['idutente'],
		    'operator' => $user->get_id(),
		    'nomefile' => $filename,
		    'codice' => $fileKey
		));
	}
}