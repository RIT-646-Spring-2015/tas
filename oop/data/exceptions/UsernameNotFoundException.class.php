<?php

/**
 * This exception is thrown when a username is not found
 */
class UsernameNotFoundException extends Exception
{

    public function __construct( $username )
    {
        parent::__construct( sprintf( 'Username \'%s\' not found.', $username ) );
    }

    public function __toString()
    {
        return sprintf( '%s: %s', __CLASS__, $this->message );
    }
}
?>