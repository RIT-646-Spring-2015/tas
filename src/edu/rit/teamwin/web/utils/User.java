package edu.rit.teamwin.web.utils;

import java.util.Collection;

import org.springframework.security.core.GrantedAuthority;

/**
 * @author Alex Aiezza
 */
public class User extends org.springframework.security.core.userdetails.User
{
    private static final long serialVersionUID = 1L;

    public static final User ShabaUserFromForm( final UserForm user )
    {
        return new User( user.getUsername(), user.getPassword(), user.isEnabled(),
            user.getFirst_name(), user.getLast_name(), user.getEmail(), user.getDate_joined(),
            user.getLast_online(), user.isAccountNonExpired(), user.isCredentialsNonExpired(),
            user.isAccountNonLocked(), user.getAuthorities() );


    }

    private final String first_name;

    private final String last_name;

    private final String email;

    private final String date_joined;

    private final String last_online;

    public User(
            final String username,
            final String password,
            final boolean enabled,
            final String first_name,
            final String last_name,
            final String email,
            final String date_joined,
            final String last_online,
            final boolean accountNonExpired,
            final boolean credentialsNonExpired,
            final boolean accountNonLocked,
            final Collection<? extends GrantedAuthority> authorities )
    {
        super( username, password, enabled, accountNonExpired, credentialsNonExpired,
            accountNonLocked, authorities );

        this.first_name = first_name;
        this.last_name = last_name;
        this.email = email;
        this.date_joined = date_joined;
        this.last_online = last_online;
    }

    public User(
            final String username,
            final String password,
            final String first_name,
            final String last_name,
            final String email,
            final String date_joined,
            final String last_online,
            final Collection<? extends GrantedAuthority> authorities )
    {
        this( username, password, true, first_name, last_name, email, date_joined, last_online,
            true, true, true, authorities );
    }

    // No need to also override hashcode since User super class already does.
    @Override
    public boolean equals( final Object rhs )
    {
        if ( rhs instanceof User )
            return getUsername().equals( ( (User) rhs ).getUsername() );
        if ( rhs instanceof String )
            return getUsername().equals( rhs );
        return false;
    }

    /**
     * @return the date_joined
     */
    public String getDate_joined()
    {
        return date_joined;
    }

    /**
     * @return the email
     */
    public String getEmail()
    {
        return email;
    }

    /**
     * @return the first_name
     */
    public String getFirst_name()
    {
        return first_name;
    }

    /**
     * @return the last_name
     */
    public String getLast_name()
    {
        return last_name;
    }

    /**
     * @return the last_online
     */
    public String getLast_online()
    {
        return last_online;
    }

    @Override
    public String toString()
    {
        return String.format( "%s (%s %s)", getUsername(), first_name, last_name );
    }
}
