<?php

require 'MemberDetails.class.php';

final class Member extends MemberDetails
{
    public static function userFromForm( MemberForm $user )
    {
        return new Member( $user->getUsername(), $user->getPassword(),
                $user->getFirst_name(), $user->getLast_name(), $user->getEmail(), $user->getDate_joined(),
                $user->getLast_online(), $user->isEnabled(), $user->getAuthorities() );
    }
}
?>