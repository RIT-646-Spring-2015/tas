<?php
require PROJECT_ROOT . '/oop/CourseDetails.class.php';
require_once 'User.class.php';

final class Course extends CourseDetails
{

    public static function courseFromForm( CourseForm $form )
    {
        return new Course( $form->getNumber(), $form->getName(), $form->getEnrolled(), 
                $form->getTopics() );
    }

    public function addUser( $username, $role )
    {
        $this->enrolled[$username] = $role;
    }
}
?>