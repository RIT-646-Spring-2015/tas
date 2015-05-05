<?php
require dirname( __FILE__ ) . '/../Course.class.php';

final class CourseMapper
{

    public static function mapRow( $rs )
    {
        $enrolled = $rs['Username'] == null ? array () : array ( $rs['Username'] );
        
        $course = new Course( $rs['Number'], $rs['Name'], $enrolled );
        
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
                $inCourse = &$results[$course->getNumber()];
                
                $enrolled = &$inCourse->getEnrolled();
                $enrolled = array_merge( $enrolled, $course->getEnrolled() );
            } else
            {
                $results[$course->getNumber()] = $course;
            }
        }
        
        return $results;
    }
}
?>
