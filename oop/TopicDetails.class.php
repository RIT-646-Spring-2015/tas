<?php

abstract class TopicDetails
{

    protected $name;

    protected $link;

    protected $submissionDate;

    protected $blacklisted;

    protected $status;

    /**
     * Constructs a new topic object
     *
     * @param string $name
     *            the topic name.
     * @param string $link
     *            the topic link.
     * @param string $submissionDate
     *            the topic submission date.
     * @param string $blacklisted
     *            whether or not the topic is blacklisted.
     * @param number $status
     *            the topic's status.
     */
    public function __construct( $name, $link, $submissionDate, $blacklisted, $status )
    {
        $this->name = $name;
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