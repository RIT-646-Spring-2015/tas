<?php

class TopicFormValidator
{

    /**
     * Validates a new topic
     *
     * @param TopicForm $topic            
     * @return array
     */
    public static function validateRequiredFields( TopicForm $topic )
    {
        $errors = array ();
        self::validates( $errors, $topic->getName(), 'Topic name required.' );
        self::validates( $errors, $topic->getSubmittingUsername(), 'Submitting User required.' );
        self::validates( $errors, $topic->getCourseNumber(), 'Course number required.' );
        
        return $errors;
    }

    public static function validate( TopicForm $topic )
    {
        global $TAS_DB_MANAGER;
        
        $errors = array ();
        
        // Validate Topic Name
        if ( !preg_match( '/[\w\p{P}\s{1}:]{3,60}$/', $topic->getName() ) )
        {
            $errors[] .= 'Topic name must only be 3 to 60 characters';
        }
        
        // Validate Topic Link
        if ( !preg_match( '/.{0,500}$/', $topic->getLink() ) )
        {
            $errors[] .= 'Link must be less than 500 characters';
        }
        
        try
        {
            // Validate user exists
            $user = $TAS_DB_MANAGER->loadUserByUsername( $topic->getSubmittingUsername() );
            // Validate Course exists
            $course = $TAS_DB_MANAGER->loadCourseByNumber( $topic->getCourseNumber() );
            // Validate User is in course
            if ( !array_key_exists( $user->getUsername(), $course->getEnrolled() ) )
            {
                $errors[] .= sprintf( 'User: \'%s\' is not enrolled in Course: \'%s\'', 
                        $user->getUsername(), $course->getNumber() );
            }
        } catch ( Exception $e )
        {
            $errors[] .= $e->getMessage();
        }
        
        /*
         * Validate Status
         * $statuses = $TAS_DB_MANAGER->getAvailableStatuses();
         * if ( !in_array( $topic->getStatus(), $statuses ) )
         * {
         * $errors[] .= sprintf( 'Status=%s| Status must only be one of the following: [%s].',
         * $topic->getStatus(), implode( ', ', $statuses ) );
         * }
         */
        
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
