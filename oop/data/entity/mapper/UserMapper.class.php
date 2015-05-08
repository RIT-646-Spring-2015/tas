<?php
require dirname( __FILE__ ) . '/../User.class.php';

final class UserMapper
{

    public static function mapRow( $rs )
    {
        if ( ( $auth = $rs['AuthorityName'] ) != null )
            $auths = array ( $auth );
        else $auths = array ();
        
        $user = new User( $rs['Username'], $rs['Password'], $rs['FirstName'], $rs['LastName'], 
                $rs['Email'], $rs['DateJoined'], $rs['LastOnline'], $rs['Enabled'], $auths );
        
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
                $inUser = &$results[$user->getUsername()];
                
                $auths = &$inUser->getAuthorities();
                $auths = array_merge( $auths, $user->getAuthorities() );
            } else
            {
                $results[$user->getUsername()] = $user;
            }
        }
        
        return $results;
    }
}
?>