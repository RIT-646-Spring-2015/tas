<?php
include_once 'TopicDetails.class.php';

final class TopicForm extends TopicDetails
{

    public function __construct( $name, $link, $submissionDate, $blacklisted, $status )
    {
        parent::__construct( $name, $link, $submissionDate, $blacklisted, $status );
    }

    /**
     * Set the topic's name.
     */
    public function setName( $name )
    {
        $this->name = $name;
    }

    /**
     * Set the topic's link.
     */
    public function setLink( $link )
    {
        $this->link = $link;
    }

    /**
     * Set the topic submission date.
     */
    public function setSubmissionDate( $submissionDate )
    {
        $this->submissionDate = $submissionDate;
    }

    /**
     * Set whether or not the topic is blacklisted.
     */
    public function setBlacklisted( $blacklisted )
    {
        $this->blacklisted = $blacklisted;
    }

    /**
     * Set the topic status
     */
    public function setStatus( $status )
    {
        $this->status = $status;
    }
}
?>