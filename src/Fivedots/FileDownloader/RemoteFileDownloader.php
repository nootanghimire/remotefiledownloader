<?php

namespace Fivedots\FileDownloader;

use Fivedots\FileDownloader\Exceptions\InvalidDestinationException;
use Fivedots\FileDownloader\Exceptions\RemoteFileDownloaderException;
use Fivedots\FileDownloader\Exceptions\InvalidSourceException;

/**
 *  Downloads files remotely
 * @package RemoteFileDownloader
 * @author Samundra Shrestha
 * @date September 19, 2013
 */
class RemoteFileDownloader
{

    protected $_collections = array();
    protected $prefix;
    protected $suffix;
    protected $_current_queue;
    protected $preserve_filename = false;
    protected $create;
    protected $basedir;
    protected $_dirname;
    protected $_error;
    protected $_files;

    /**
     * Initialises instance
     *
     * @param string $prefix Prefix that is appended to the target filename
     * @param string $suffix Suffix that is appended to the target filename
     * @param boolean $preserve_filename If false, filename is computed
     * dynamically by the system
     */
    public function __construct($prefix = '', $suffix = '', $preserve_filename = false)
    {
        $this->basedir = __DIR__;

        if (!empty($prefix)) {
            $this->prefix = $prefix;
        }
        if (!empty($suffix)) {
            $this->suffix = $suffix;
        }

        if ($preserve_filename) {
            $this->preserve_filename = $preserve_filename;
        }
    }

    /**
     * Sources of the files to download. Currently this function accepts string
     * and array as source. Object is not supported. If object is passed then function
     * throws the InvalidSourceException which user has to manually catch.
     *
     * @param mixed $collections <p>can be array or string</p>
     * @return array Returns the updated collections
     * @throws InvalidSourceException <p>Exception thrown when source is not supported</p>
     */
    public function set_sources($collections)
    {
        if (is_object($collections)) {
            throw new InvalidSourceException("Object collection is not supported", 3);
        }
        if (is_array($collections)) {
            $this->_collections = $collections;
        } else {
            $this->_collections = array($collections);
        }
        return $this->_collections;
    }

    /**
     * Initiate the actual download
     * @param boolean $is_new <p>Set this to new when you are newly creating
     * a download collections. If you want the downloaded images to be appended
     * with new collection list, set this to false.</p>
     *
     * @see filewriter
     * @return array All the downloaded files
     */
    public function init($is_new = true)
    {

        // Empty the array for the first time
        if ($is_new === true) {
            $this->_files = array();
        }

        foreach ($this->_collections as $item) {
            $file = $item;
            $this->_current_queue = pathinfo($file);
            ob_clean();
            ob_start(array(__CLASS__, 'filewriter'));
            $contents = file_get_contents($file);

            // The source url is invalid so, couldn't download the image
            if ($contents == false) {
                ob_end_flush();
                $this->_error[] = $file;
                continue;
            }
            echo $contents;
            ob_end_flush();
        }

        return $this->_files;
    }

    /**
     * Writes output buffer to the file.
     *
     * @param mixed $buffer Output buffer which is in the memory
     * @see init
     */
    public function filewriter($buffer)
    {
        if ($this->preserve_filename == true) {
            $filename = '';
            if ($this->prefix) {
                $filename .= $this->prefix;
            }
            $filename .= mt_rand();
            if ($this->suffix) {
                $filename .= $this->suffix;
            }
            $ext = isset($this->_current_queue['extension']) ? $this->_current_queue['extension'] : 'jpg';
            $filename .= '.' . $ext;

        } else {
            $fn = $this->_current_queue['filename'];
            $ext = isset($this->_current_queue['extension']) ? $this->_current_queue['extension'] : 'jpg';

            $filename = md5($fn . mt_rand() . rand(5, 15)) . '.' . $ext;

        }
        $this->_files[] = $filename;

        $file_path = $this->_dirname . $filename;

        file_put_contents($file_path, $buffer);

        // Just give some time to other PHP files as well
        // Sleep for 100 microsecond
        // TODO: This will delay the execution and requires optimizations
        usleep(100);
    }

    /**
     * <p>Sets the destination path, if second parameter is false then destination
     * path is not created.</p>
     * @param string $path destination path
     * @param int $mode File permission mode
     * @param boolean $recursive <p>If true, the destination directory is created, if it doesn't exists. </p>
     * @return string Destination path
     * @throws InvalidDestinationException
     * @throws RemoteFileDownloaderException
     *
     */
    public function set_destination_path($path, $mode = 0755, $recursive = false)
    {
        $this->create = $recursive;

        if (file_exists($path) === false && $recursive === false) {
            throw new InvalidDestinationException('Remotefiledownloader::Destination does not exists', 2);
        }

        if ($recursive === true) {
            if (!file_exists($path)) {

                if (!mkdir($path, $mode, true)) {
                    throw new RemoteFileDownloaderException('Remotefiledownloader::Unable to create destination directory.', 3);
                }
            }
        }
        $this->_dirname = $path;
        return $this->_dirname;
    }

}

?>