<?php
require PROJECT_ROOT . '/oop/UserDetails.class.php';

final class User extends UserDetails
{

    private $coursesEnrolledIn;

    public function __construct( $username, $password, $firstname, $lastname, $email, $date_joined, 
            $last_online, $enabled, $authorities = array(), $coursesEnrolledIn = array() )
    {
        parent::__construct( $username, $password, $firstname, $lastname, $email, $date_joined, 
                $last_online, $enabled, $authorities );
        
        $this->coursesEnrolledIn = $coursesEnrolledIn;
    }

    public function &getCoursesEnrolledIn()
    {
        return $this->coursesEnrolledIn;
    }

    public static function userFromForm( UserForm $user )
    {
        return new User( $user->getUsername(), $user->getPassword(), $user->getFirst_name(), 
                $user->getLast_name(), $user->getEmail(), $user->getDate_joined(), 
                $user->getLast_online(), $user->isEnabled(), $user->getAuthorities() );
    }
}
?>