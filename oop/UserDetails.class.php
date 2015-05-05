<?php

abstract class UserDetails
{
    protected $username;
    protected $password;
    protected $firstname;
    protected $lastname;
    protected $email;
    protected $date_joined;
    protected $last_online;
    protected $enabled;
    protected $authorities;

    /**
     * Constructs a new user object
     *
     * @param string $username
     *            the user's unique username.
     * @param string $password
     *            the user's password.
     * @param string $firstname
     *            the user's firstname.
     * @param string $lastname
     *            the user's lastname.
     * @param string $email
     *            the user's email.
     * @param number $date_joined
     *            the date the user joined.
     * @param number $last_online
     *            the date the user was last online.
     * @param bool $enabled
     *            whether or not the user's account is enabled.
     * @param array $authorities
     *            the user's authorities.
     */
    public function __construct( $username, $password, $firstname, $lastname, $email,
        $date_joined, $last_online, $enabled, $authorities = array() )
    {
        $this->username = $username;
        $this->password = $password;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->date_joined = $date_joined;
        $this->last_online = $last_online;
        $this->enabled = $enabled;
        $this->authorities = $authorities;
    }

    /**
     * Get the user's username.
     *
     * @return the user's username.
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Get the user's first name.
     *
     * @return the user's first name.
     */
    public function getFirstname()
    {
        return $this->firstname;
    }
    
    /**
     * Get the user's last name.
     *
     * @return the user's last name.
     */
    public function getLastname()
    {
        return $this->lastname;
    }
    
    /**
     * Get the user's email.
     *
     * @return the user's email.
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Get the user's password.
     *
     * @return the user's password.
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return the date_joined
     */
    public function getDate_joined()
    {
        return $this->date_joined;
    }

    /**
     * @return the last_online
     */
    public function getLast_online()
    {
        return $this->last_online;
    }

    /**
     * @return is the user is enabled
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return user authorites
     */
    public function &getAuthorities()
    {
        return $this->authorities;
    }
    
    public function hasRole( $role )
    {
        return in_array( $role, $this->authorities );
        
    }
    
    public function toString()
    {
        return sprintf( "%s (%s %s) <%s> [%s]", $this->username, $this->firstname, $this->lastname, 
                $this->email, implode( ', ', $this->authorities ) );
    }

    public function clearCredentials()
    {
        $this->password = "";
    }
}

?>