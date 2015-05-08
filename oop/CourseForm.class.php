<?php
include_once PROJECT_ROOT . '/oop/CourseDetails.class.php';

final class CourseForm extends CourseDetails
{

    public function __construct( $number, $name, $enrolled = array(), $topics = array() )
    {
        parent::__construct( $number, $name, $enrolled, $topics );
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

    /**
     * Set the course's topics.
     */
    public function setTopics( $topics )
    {
        $this->topics = $topics;
    }
}
?>