<?php
require dirname( __FILE__ ) . '/../Topic.class.php';

final class TopicMapper
{

    public static function mapRow( $rs )
    {
        $topic = new Topic( $rs['Name'], $rs['SubmittingUsername'], $rs['CourseNumber'], $rs['Link'], 
                $rs['SubmissionDate'], $rs['Blacklisted'], $rs['Status'] );
        
        return $topic;
    }

    public static function extractData( $rs )
    {
        $results = array ();
        
        while ( $res = $rs->fetchArray( SQLITE3_ASSOC ) )
        {
            if ( !isset( $res['Name'] ) )
                continue;
            
            $topic = self::mapRow( $res );
            
            if ( array_key_exists( $topic->getName(), $results ) )
            {
                $inTopic = &$results[$topic->getName()];
            } else
            {
                $results[$topic->getName()] = $topic;
            }
        }
        
        return $results;
    }
}
?>