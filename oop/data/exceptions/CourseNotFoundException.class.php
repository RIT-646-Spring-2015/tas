<?php

/**
 * This exception is thrown when a Course is not found
 */
class CourseNotFoundException extends Exception
{
    public function __construct( $courseName )
    {
        parent::__construct( sprintf( 'Course \'%s\' not found.', $courseName ) );
    }
    
    public function __toString()
    {
        return sprintf( '%s: %s', __CLASS__, $this->message );
    }
}
?>