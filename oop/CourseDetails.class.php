<?php

abstract class CourseDetails
{

    protected $number;

    protected $name;

    protected $usersInvolved;

    /**
     * Constructs a new product object
     *
     * @param string $number
     *            the product's unique productId.
     * @param string $name
     *            the product's name.
     * @param array $usersInvolved
     *            the product's description.
     */
    public function __construct( $number, $name, $usersInvolved )
    {
        $this->number = $number;
        $this->name = $name;
        $this->usersInvolved = $usersInvolved;
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
     * Get the users involved in this course;
     *
     * @return the users involved in this course;
     */
    public function getUsersInvolved()
    {
        return $this->usersInvolved;
    }

    public function toString()
    {
        return sprintf( "Course: %d: %s", $this->number, $this->name );
    }
}

?>