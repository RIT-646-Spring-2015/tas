<?php
include_once PROJECT_ROOT . '/oop/CourseDetails.class.php';

final class CourseForm extends CourseDetails
{

    public function __construct( $number, $name, $enrolled = array() )
    {
        parent::__construct( $number, $name, $enrolled );
    }

    /**
     * Set the course's number.
     */
    public function setNumber( $number )
    {
        $this->number = $number;
    }

    /**
     * Set the course's name.
     */
    public function setName( $name )
    {
        $this->name = $name;
    }

    /**
     * Set the course's enrolled users.
     */
    public function setEnrolled( $enrolled )
    {
        $this->enrolled = $enrolled;
    }
}
?>