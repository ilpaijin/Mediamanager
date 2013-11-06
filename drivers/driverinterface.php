<?php

namespace Qoffice\Services\Mediamanager\Drivers;

interface DriverInterface
{
	public function uploadFile();
	public function deleteFile($idutente, $file);
}