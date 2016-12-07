<?php
/**
 * Created by PhpStorm.
 * User: jthomann
 * Date: 12/6/16
 * Time: 7:30 PM
 */

namespace Utilities;


class JsonWrapper {
    /** @var  $status (Sets to success or error) */
    private $status;
    /** @var  $data (A string or array of data) */
    private $data;
    /** @var  $message (Additional information) */
    private $message;

    /**
     * RatingResponse constructor.
     */
    public function __construct() { }

    /**
     * Set the status
     * @param $status (Sets to success or error)
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * Set the data
     * @param $data (A string or array of data)
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * Set the message
     * @param $message (Additional information)
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * Convert class to an array.
     * @return (array)
     */
    public function toArray() {
        $arr = array(
            "status" => $this->status,
            "data" => $this->data,
            "message" => $this->message
        );
        return $arr;
    }
}