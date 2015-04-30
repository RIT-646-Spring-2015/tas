<?php
require '../../CourseDetails.class.php';
require_once 'User.class.php';

final class Course extends CourseDetails
{

    public static function courseFromForm( CourseForm $form )
    {
        return new Course( $form->getNumber(), $form->getName(), array () );
    }

    public function addUser( User $user )
    {
        $this->usersInvolved[] = $user;
    }
}
?>