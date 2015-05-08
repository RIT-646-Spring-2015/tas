<?php
require dirname( __FILE__ ) . '/../Course.class.php';

final class CourseMapper
{

    public static function mapRow( $rs )
    {
        $enrolled = $rs['Username'] == null ? array () : array ( $rs['Username'] => $rs['Role'] );
        
        $topics = $rs['TopicName'] == null ? array () : array ( 
                        $rs['TopicName'] => $rs['SubmittingUsername'] );
        
        $course = new Course( $rs['Number'], $rs['CourseName'], $enrolled, $topics );
        
        return $course;
    }

    public static function extractData( $rs )
    {
        $results = array ();
        
        while ( $res = $rs->fetchArray( SQLITE3_ASSOC ) )
        {
            if ( !isset( $res['Number'] ) )
                continue;
            
            $course = self::mapRow( $res );
            
            if ( array_key_exists( $course->getNumber(), $results ) )
            {
                // combine list of enrolled users
                $inCourse = &$results[$course->getNumber()];
                
                $enrolled = &$inCourse->getEnrolled();
                $enrolled = array_merge_recursive_distinct( $enrolled, $course->getEnrolled() );
                
                // combine list of topics
                $topics = &$inCourse->getTopics();
                $topics = array_merge_recursive_distinct( $topics, $course->getTopics() );
            } else
            {
                $results[$course->getNumber()] = $course;
            }
        }
        
        return $results;
    }
}
?>
