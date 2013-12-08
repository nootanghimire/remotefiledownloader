<?php

require "remotefiledownloader.php";

// Source images
$images = array(
	'http://localhost/assets/images/ideal-prop-logo.png',
	'http://localhost/assets/images/ideal-prop-logo.png',
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
$r->set_destination($image_dir, $mode, $recursive);

$obj = new Stdclass;


// Add your source files, images
$r->set_sources($obj);


try {
    // Begin the downloading and then wait for returned array
	$files = $r->init();

    // Get the recently downloaded images files as array
	print_r($files);
} catch (InvalidSourceException $e) {
    // Invalid source
} catch (InvalidDestinationException $e) {
    // Invalid destination
} catch (Exception $e) {
    // Some other error here
}
?>