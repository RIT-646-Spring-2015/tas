package edu.rit.teamwin.web.utils;

import static org.apache.commons.lang.StringUtils.isEmpty;

import java.util.ArrayList;

import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.userdetails.UserDetails;


/**
 * This class is only for passing a signup form object
 *
 * @author Alex Aiezza
 */
@SuppressWarnings ( "serial" )
public class UserForm implements UserDetails
{
    /**
     *
     */
    private static final long                 serialVersionUID = 1L;

    private transient String                  password;

    private String                            first_name;

    private String                            last_name;

    private String                            email;

    private String                            date_joined;

    private String                            last_online;

    private transient String                  confirmPassword;

    private String                            username;

    private boolean                           enabled;

    private ArrayList<SimpleGrantedAuthority> authorities;

    public void clearPassword()
    {
        password = "";
        confirmPassword = "";
    }

    @Override
    public ArrayList<SimpleGrantedAuthority> getAuthorities()
    {
        return authorities;
    }

    /**
     * @return the confirmPassword
     */
    public String getConfirmPassword()
    {
        return confirmPassword;
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

    /**
     * @return the password
     */
    @Override
    public String getPassword()
    {
        return password;
    }

    /**
     * @return the username
     */
    @Override
    public String getUsername()
    {
        return username;
    }

    @Override
    public boolean isAccountNonExpired()
    {
        // TODO Auto-generated method stub
        return false;
    }

    @Override
    public boolean isAccountNonLocked()
    {
        // TODO Auto-generated method stub
        return false;
    }

    public boolean isBlank()
    {
        return isEmpty( first_name ) && isEmpty( last_name ) && isEmpty( getUsername() ) &&
                isEmpty( email );
    }

    @Override
    public boolean isCredentialsNonExpired()
    {
        // TODO Auto-generated method stub
        return false;
    }

    @Override
    public boolean isEnabled()
    {
        return enabled;
    }

    public void setAuthorities( final ArrayList<SimpleGrantedAuthority> authorities )
    {
        this.authorities = authorities;
    }

    /**
     * @param confirmPassword
     *            the confirmPassword to set
     */
    public void setConfirmPassword( final String confirmPassword )
    {
        this.confirmPassword = confirmPassword;
    }

    /**
     * @param date_joined
     *            the date_joined to set
     */
    public void setDate_joined( final String date_joined )
    {
        this.date_joined = date_joined;
    }

    /**
     * @param email
     *            the email to set
     */
    public void setEmail( final String email )
    {
        this.email = email;
    }

    /*
     * public void setAuthorities( ArrayList<String> authorities ) { for (
     * String auth : authorities ) { this.authorities.add( new
     * SimpleGrantedAuthority( auth ) ); } }
     */

    public void setEnabled( final boolean enabled )

    {
        this.enabled = enabled;
    }

    /**
     * @param first_name
     *            the first_name to set
     */
    public void setFirst_name( final String first_name )
    {
        this.first_name = first_name;
    }

    /**
     * @param last_name
     *            the last_name to set
     */
    public void setLast_name( final String last_name )
    {
        this.last_name = last_name;
    }

    /**
     * @param last_online
     *            the last_online to set
     */
    public void setLast_online( final String last_online )
    {
        this.last_online = last_online;
    }

    /**
     * @param password
     *            the password to set
     */
    public void setPassword( final String password )
    {
        this.password = password;
    }

    /**
     * @param username
     *            the username to set
     */
    public void setUsername( final String username )
    {
        this.username = username;
    }

    @Override
    public String toString()
    {
        return username;
    }
}
