<?php
require dirname( __FILE__ ) . '/../User.class.php';

final class UserMapper
{

    public static function mapRow( $rs )
    {
        if ( ( $auth = $rs['AuthorityName'] ) != null )
            $auths = array ( $auth );
        else $auths = array ();
        
        if ( ( $course = $rs['CourseNumber'] ) != null )
            $courses = array ( $course => $rs['Role'] );
        else $courses = array ();
        
        $user = new User( $rs['Username'], $rs['Password'], $rs['FirstName'], $rs['LastName'], 
                $rs['Email'], $rs['DateJoined'], $rs['LastOnline'], $rs['Enabled'], $auths, $courses );
        
        return $user;
    }

    public static function extractData( $rs )
    {
        $results = array ();
        
        while ( $res = $rs->fetchArray( SQLITE3_ASSOC ) )
        {
            if ( !isset( $res['Username'] ) )
                continue;
            
            $user = self::mapRow( $res );
            
            if ( array_key_exists( $user->getUsername(), $results ) )
            {
                // authorities merge
                $inUser = &$results[$user->getUsername()];
                
                $auths = &$inUser->getAuthorities();
                $auths = array_merge( $auths, $user->getAuthorities() );
                $auths = array_unique( $auths );
                
                // courses merge
                $courses = &$inUser->getAuthorities();
                $courses = array_merge( $courses, $user->getCoursesEnrolledIn() );
                $courses = array_unique( $courses );
            } else
            {
                $results[$user->getUsername()] = $user;
            }
        }
        
        return $results;
    }
}
?>