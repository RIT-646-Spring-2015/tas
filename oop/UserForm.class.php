<?php
require_once PROJECT_ROOT . '/oop/UserDetails.class.php';

final class UserForm extends UserDetails
{

    private $confirmPassword;

    public function __construct( $username, $password, $confirmPassword, $firstName, $lastName, 
            $email, $dateJoined, $lastOnline, $enabled, $authorities = array() )
    {
        $this->confirmPassword = $confirmPassword;
        parent::__construct( $username, $password, $firstName, $lastName, $email, $dateJoined, 
                $lastOnline, $enabled, $authorities );
    }

    /**
     *
     * @param
     *            string username
     *            the username to set
     */
    public function setUsername( $username )
    {
        $this->username = $username;
    }

    /**
     *
     * @param
     *            string firstName
     *            the firstName to set
     */
    public function setFirstName( $firstName )
    {
        $this->firstName = $firstName;
    }

    /**
     *
     * @param
     *            string last_name
     *            the last_name to set
     */
    public function setLastName( $lastName )
    {
        $this->lastName = $lastName;
    }

    /**
     *
     * @param
     *            string email
     *            the email to set
     */
    public function setEmail( $email )
    {
        $this->email = $email;
    }

    /**
     *
     * @param
     *            string password
     *            the password to set
     */
    public function setPassword( $password )
    {
        $this->password = $password;
    }

    /**
     *
     * @param
     *            string confirmPassword
     *            the confirmPassword to set
     */
    public function setConfirmPassword( $confirmPassword )
    {
        $this->confirmPassword = $confirmPassword;
    }

    /**
     *
     * @return the user form confirmed password
     */
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     *
     * @param
     *            number dateJoined the dateJoined to set
     */
    public function setDateJoined( $dateJoined )
    {
        $this->dateJoined = $dateJoined;
    }

    /**
     *
     * @param
     *            number last_online the last_online to set
     */
    public function setLastOnline( $lastOnline )
    {
        $this->lastOnline = $lastOnline;
    }

    /**
     *
     * @param
     *            bool enabled enable user account
     */
    public function setEnabled( $enabled )
    {
        $this->enabled = $enabled;
    }

    /**
     *
     * @param
     *            boolean authorities authorites of the user
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