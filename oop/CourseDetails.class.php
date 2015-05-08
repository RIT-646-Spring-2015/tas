<?php

abstract class CourseDetails
{

    protected $number;

    protected $name;

    protected $enrolled;

    /**
     * Constructs a new course object
     *
     * @param string $number
     *            the course's unique course number.
     * @param string $name
     *            the course's name.
     * @param array $enrolled
     *            the usernames enrolled in this course.
     */
    public function __construct( $number, $name, $enrolled = array() )
    {
        $this->number = $number;
        $this->name = $name;
        $this->enrolled = $enrolled;
    }

    /**
     * Get the course's course number.
     *
     * @return the course's course number.
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get the course's name.
     *
     * @return the course's name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the users involved in this course.
     *
     * @return the usernames enrolled in this course.
     */
    public function &getEnrolled()
    {
        return $this->enrolled;
    }

    public function toString()
    {
        $enrolled = '';
        $i = 0;
        foreach ( $this->enrolled as $username => $role )
        {
            $enrolled .= $username . ':' . $role;
            if ( ++$i < count( $this->enrolled ) )
            {
                $enrolled .= ', ';
            }
        }
        return sprintf( "Course: %d: %s [%s]", $this->number, $this->name, $enrolled );
    }
}

?>