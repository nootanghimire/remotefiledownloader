<?php
require_once(__DIR__.'/../vendor/autoload.php');

use Fivedots\FileDownloader\RemoteFileDownloader;

class FileDownloaderTest extends PHPUnit_Framework_TestCase
{
    protected $sources = array();
    protected $downloader;
    protected $testDownloadPath;
    protected $mode;
    protected $recursive;
    protected $preserveFilename;
    protected $files;

    public function __construct()
    {
        parent::__construct();
        $this->mode = 0755;
        $this->recursive = true;
        $this->preserveFilename = true;
        $this->files = array();
    }

    protected function setUp()
    {
        parent::setUp();

        array_push($this->sources, 'http://202.63.240.10/userportal/pages/images/inventlogo.png');
        array_push($this->sources, 'http://i.ytimg.com/vi/zlC_KIPw1uo/0.jpg');

        //$preserveFilename = true;

        $this->downloader = new RemoteFileDownloader('prefix', 'suffix', $this->preserveFilename);

        // Change this to your target folder
        $this->testDownloadPath = 'testDownloads/';

        // Set mode for newly created files
//        $mode = 0755;

        // Create target folder if it does ont exists, recursively
//        $recursive = true;

        // Finally set the target where files will be downloaded
        $this->downloader->set_destination_path($this->testDownloadPath, $this->mode, $this->recursive);

        //$obj = new Stdclass;
        // Add your source files, images
        $this->downloader->set_sources($this->sources);
    }

    protected function tearDown()
    {
        parent::tearDown();
        foreach ($this->files as $f) {
            $full_path = realpath($this->testDownloadPath . $f);
            $state = file_exists($full_path);

            if($state)
            {
                $this->assertTrue($state);
                @unlink($full_path);
            }

        }
        unset($this->downloader);
    }

    public function testSetDestinationPath()
    {
        $destination = $this->downloader->set_destination_path($this->testDownloadPath, $this->mode, $this->recursive);
        $this->assertEquals($this->testDownloadPath, $destination);
    }

    public function testFileDownloader()
    {
        try {
            // Begin the download and then wait for returned array
            $this->files = $this->downloader->init();
            $this->assertInternalType("array", $this->files);

        } catch (InvalidSourceException $e) {
            // Invalid source
        } catch (InvalidDestinationException $e) {
            // Invalid destination
        } catch (Exception $e) {
            // Some other error here
        }
    }
}