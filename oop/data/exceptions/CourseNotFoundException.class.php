<?php

/**
 * Course Not Found Exception
 */
class CourseNotFoundException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct( $courseName )
    {
        // some code
    
        // make sure everything is assigned properly
        parent::__construct( sprintf( 'Course \'%s\' not found.', $productName ) );
    }

    // custom string representation of object
    public function __toString()
    {
        return sprintf( '%s: %s', __CLASS__, $this->message );
    }
}
?>