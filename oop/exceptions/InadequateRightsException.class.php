<?php

/**
 * Define a custom exception class
 */
class InadequateRightsException extends Exception
{
    // custom string representation of object
    public function __toString()
    {
        return sprintf( '%s: %s', __CLASS__, $this->message );
    }
}
?>