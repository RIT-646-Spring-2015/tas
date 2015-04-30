<?php

require '../../UserDetails.class.php';

final class User extends UserDetails
{
    public static function userFromForm( UserForm $user )
    {
        return new User( $user->getUsername(), $user->getPassword(),
                $user->getFirst_name(), $user->getLast_name(), $user->getEmail(), $user->getDate_joined(),
                $user->getLast_online(), $user->isEnabled(), $user->getAuthorities() );
    }
}
?>