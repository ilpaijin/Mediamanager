<?php

namespace Qoffice\Services\MediaManager\Drivers;

interface DriverInterface
{
	public function uploadFile();
	public function deleteFile($idutente, $file);
}