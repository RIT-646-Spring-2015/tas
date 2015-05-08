<?php

abstract class TopicDetails
{

    protected $name;

    protected $submittingUsername;

    protected $courseNumber;

    protected $link;

    protected $submissionDate;

    protected $blacklisted;

    protected $status;

    /**
     * Constructs a new topic object
     *
     * @param string $name
     *            the topic name.
     * @param
     *            string submittingUsername
     *            the user submitting the topic
     * @param
     *            string courseNumber
     *            the course number for which the topic was submitted for
     * @param string $link
     *            the topic link.
     * @param string $submissionDate
     *            the topic submission date.
     * @param string $blacklisted
     *            whether or not the topic is blacklisted.
     * @param number $status
     *            the topic's status.
     */
    public function __construct( $name, $submittingUsername, $courseNumber, $link, $submissionDate, 
            $blacklisted, $status )
    {
        $this->name = $name;
        $this->submittingUsername = $submittingUsername;
        $this->courseNumber = $courseNumber;
        $this->link = $link;
        $this->submissionDate = $submissionDate;
        $this->blacklisted = $blacklisted;
        $this->status = $status;
    }

    /**
     * Get the tooic name.
     *
     * @return the topic name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the course number this topic was originally submitted for.
     *
     * @return the course number this topic was submitted for.
     */
    public function getCourseNumber()
    {
        return $this->courseNumber;
    }

    /**
     * Get the username of the user who submitted this topic.
     *
     * @return the username of the user who submitted this topic.
     */
    public function getSubmittingUsername()
    {
        return $this->submittingUsername;
    }

    /**
     * Get the topic link.
     *
     * @return the topic link.
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Get the topic submissionDate.
     *
     * @return the topic submissionDate.
     */
    public function getSubmissionDate()
    {
        return $this->submissionDate;
    }

    /**
     * Get whether the topic is blacklisted.
     *
     * @return whether the topic is blacklisted.
     */
    public function isBlacklisted()
    {
        return $this->blacklisted;
    }

    /**
     * Get the topic's status.
     *
     * @return the topic's status.
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function toString()
    {
        return sprintf( "%s: [%s] {Blacklisted: %b}%n Link: (%s)%n [%s]", $this->name, 
                $this->submissionDate, $this->blacklisted, $this->link, $this->status );
    }
}

?>