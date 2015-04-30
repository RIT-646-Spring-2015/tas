<?php

include_once 'MemberDetails.class.php';

final class MemberForm extends MemberDetails
{
    private $confirmPassword;
    
    public function __construct( $username, $password, $confirmPassword, $firstname, $lastname, $email,
        $date_joined, $last_online, $enabled, $authorities = array() )
    {
        $this->confirmPassword = $confirmPassword;
        parent::__construct( $username, $password, $firstname, $lastname, $email,
            $date_joined, $last_online, $enabled, $authorities );
    }

    /**
     * @param string username
     *            the username to set
     */
    public function setUsername( $username )
    {
        $this->username = $username;
    }

    /**
     * @param string first_name
     *            the first_name to set
     */
    public function setFirst_name( $first_name )
    {
        $this->first_name = $first_name;
    }

    /**
     * @param string last_name
     *            the last_name to set
     */
    public function setLast_name( $last_name )
    {
        $this->last_name = $last_name;
    }

    /**
     * @param string email
     *            the email to set
     */
    public function setEmail( $email )
    {
        $this->email = $email;
    }

    /**
     * @param string password
     *            the password to set
     */
    public function setPassword( $password )
    {
        $this->password = $password;
    }

    /**
     * @param string confirmPassword
     *            the confirmPassword to set
     */
    public function setConfirmPassword( $confirmPassword )
    {
        $this->confirmPassword = $confirmPassword;
    }

    /**
     * @return the user form confirmed password
     */
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * @param number date_joined the date_joined to set
     */
    public function setDate_joined( $date_joined )
    {
        $this->date_joined = $date_joined;
    }

    /**
     * @param number last_online the last_online to set
     */
    public function setLast_online( $last_online )
    {
        $this->last_online = $last_online;
    }

    /**
     * @param bool enabled enable user account
     */
    public function setEnabled( $enabled )
    {
        $this->enabled = $enabled;
    }

    /**
     * @param boolean authorities authorites of the user
     */
    public function setAuthorities( $authorities )
    {
        $this->authorities = $authorities;
    }
    
    /**
     * Clear a user's password for security purposes
     */
    public function clearCredentials()
    {
        parent::clearCredentials();
        $this->confirmPassword = "";
    }
}
?>