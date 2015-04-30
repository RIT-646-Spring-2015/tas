<?php

/**
 * Define a custom exception class
 */
class TopicNotFoundException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct( $topicName )
    {
        // some code
    
        // make sure everything is assigned properly
        parent::__construct( sprintf( 'Topic \'%s\' not found.', $productName ) );
    }

    // custom string representation of object
    public function __toString()
    {
        return sprintf( '%s: %s', __CLASS__, $this->message );
    }
}
?>