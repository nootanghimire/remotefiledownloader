<?php

require_once __DIR__.'../vendor/autoload.php';

use Fivedots\FileDownloader\RemoteFileDownloader;

$images = array(
	'http://202.63.240.10/userportal/pages/images/inventlogo.png',
	'http://i.ytimg.com/vi/zlC_KIPw1uo/0.jpg',
	);

// Initialise our downloader
$r = new RemoteFileDownloader('prefix', 'suffix', true);

// Change this to your target folder
$image_dir = 'testDownloads/'; 

// Set mode for newly created files
$mode = 0755;

// Create target folder if it does ont exists, recursively
$recursive = true;

// Finally set the target where files will be downloaded
$r->set_destination_path($image_dir, $mode, $recursive);

//$obj = new Stdclass;
// Add your source files, images
$r->set_sources($images);

try {
    // Begin the download and then wait for returned array
	$files = $r->init();
    // Get the recently downloaded images files as array
	print '<pre>';
	print_r($files);

} catch (InvalidSourceException $e) {
    // Invalid source
} catch (InvalidDestinationException $e) {
    // Invalid destination
} catch (Exception $e) {
    // Some other error here
}
?>