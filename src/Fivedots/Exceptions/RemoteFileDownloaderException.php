<?php

namespace Fivedots\Exceptions;
use \Exception;
/**
 * <b>RemoteFileDownloaderException</b> thrown when downloader meets any exception
 * @link http://php.net/manual/en/class.exception.php
 */
class RemoteFileDownloaderException extends Exception {

    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}