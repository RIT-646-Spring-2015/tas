<?php

/**
 * This exception is thrown when a Topic could not be found
 */
class TopicNotFoundException extends Exception
{
    public function __construct( $topicName )
    {
        parent::__construct( sprintf( 'Topic \'%s\' not found.', $topicName ) );
    }
    
    public function __toString()
    {
        return sprintf( '%s: %s', __CLASS__, $this->message );
    }
}
?>