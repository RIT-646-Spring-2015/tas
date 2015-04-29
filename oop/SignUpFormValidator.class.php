<?php

class SignUpFormValidator
{

    const ACCEPTABLE_USERNAME = '/^[\w]{3,16}$/';
    
    /**
     * Validates a new user
     *
     * @param MemberForm $user            
     * @return array
     */
    public static function validateRequiredFields( MemberForm $user )
    {
        $errors = array();
        self::validates( $errors, $user->getFirstname(), 'First name required.' );
        self::validates( $errors, $user->getLastname(), 'Last name required.' );
        self::validates( $errors, $user->getEmail(), 'Email required.' );
        self::validates( $errors, $user->getUsername(), 'Username required.' );
        
        return $errors;
    }

    public static function validate( MemberForm $user )
    {
        $errors = array();

        /* This condition of a username containing a space
         * is covered in the regex match to follow, however
         * explicitely checking for it allows me to be more
         * informative for the user.
         */
        if ( strpos( $user->getUsername(), ' ' ) > 0 )
        {
            $errors[] .= 'sername contains space';
        }

        if ( !preg_match( '/^[\w]{3,16}$/', $user->getUsername() ) )
        {
            $errors[] .= 'Username must only be 3 to 16 characters [a-zA-Z0-9_]';
        }
        
        if ( count( $errors ) != 0 )
            return $errors;
        
        self::validates( $errors, $user->getPassword(), 'Password required.' );
        
        if ( $user->getPassword() != $user->getConfirmPassword() )
        {
            $errors[] .= 'Passwords Must Match';
        }
        
        return $errors;
    }
    
    /**
     *
     * @param array $errors            
     * @param string $field            
     * @param string $errorStatement            
     */
    static function validates( &$errors, $field, $errorStatement )
    {
        if ( empty( $field ) && !is_numeric( $field ) )
        {
            $errors[] .= $errorStatement;
        }
    }
}

?>
