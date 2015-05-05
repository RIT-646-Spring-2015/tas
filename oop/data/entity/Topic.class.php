<?php
require PROJECT_ROOT. '/oop/TopicDetails.class.php';

final class Topic extends TopicDetails
{

    public static function topicFromForm( TopicForm $form )
    {
        return new Topic( $form->getName(), $form->getLink(), $form->getSubmissionDate(), 
                $form->isBlacklisted(), $form->getStatus() );
    }
}
?>