<?php

abstract class CourseDetails
{

    protected $number;

    protected $name;

    protected $enrolled;

    protected $topics;

    /**
     * Constructs a new course object
     *
     * @param string $number
     *            the course's unique course number.
     * @param string $name
     *            the course's name.
     * @param array $enrolled
     *            the usernames enrolled in this course mapped to their roles
     * @param
     *            array topics
     *            the topic names proposed for this course mapped to their user
     */
    public function __construct( $number, $name, $enrolled = array(), $topics = array() )
    {
        $this->number = $number;
        $this->name = $name;
        $this->enrolled = $enrolled;
        $this->topics = $topics;
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

    /**
     * Get the topic names proposed for this course.
     *
     * @return the topic names proposed for this course.
     */
    public function &getTopics()
    {
        return $this->topics;
    }

    public function toString()
    {
        $enrolled = '';
        $i = 0;
        foreach ( $this->enrolled as $username => $role )
        {
            if ( $i == 0 )
                $enrolled .= ' [';
            
            $enrolled .= $username . ':' . $role;
            if ( ++$i < count( $this->enrolled ) )
            {
                $enrolled .= ', ';
            } else
            {
                $enrolled .= ']';
            }
        }
        
        $topics = '';
        $i = 0;
        foreach ( $this->topics as $topicName => $username )
        {
            if ( $i == 0 )
                $topics .= ' [';
            
            $topics .= $topicName . ':' . $username;
            if ( ++$i < count( $this->topics ) )
            {
                $topics .= ', ';
            } else
            {
                $topics .= ']';
            }
        }
        return sprintf( "Course: %s: %s%s%s", $this->number, $this->name, $enrolled, $topics );
    }
}

?>