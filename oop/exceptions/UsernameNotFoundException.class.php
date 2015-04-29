<?php

/**
 * Define a custom exception class
 */
class UsernameNotFoundException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct( $username )
    {
        // some code
    
        // make sure everything is assigned properly
        parent::__construct( sprintf( 'Username \'%s\' not found.', $username ) );
    }

    // custom string representation of object
    public function __toString()
    {
        return sprintf( '%s: %s', __CLASS__, $this->message );
    }
}
?>