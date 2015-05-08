<?php

class CourseFormValidator
{

    /**
     * Validates a new course
     *
     * @param CourseForm $course            
     * @return array
     */
    public static function validateRequiredFields( CourseForm $course )
    {
        $errors = array ();
        self::validates( $errors, $course->getNumber(), 'Course Number required.' );
        self::validates( $errors, $course->getName(), 'Course Name required.' );
        
        return $errors;
    }

    public static function validate( CourseForm $course )
    {
        global $TAS_DB_MANAGER;
        
        $errors = array ();
        
        // Validate Course Number
        if ( !preg_match( '/^[\w\p{P}:-]{3,15}$/', $course->getNumber() ) )
        {
            $errors[] .= 'Course Number must only be 3 to 15 characters [a-zA-Z0-9_-\':]';
        }
        
        // Validate Course Name
        if ( !preg_match( '/^[\w\p{P}\s:-]{1,60}$/', $course->getName() ) )
        {
            $errors[] .= 'Course Name must be less than 60 characters [a-zA-Z0-9_ -\':]';
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
