package edu.rit.teamwin.web.utils;

import static java.lang.String.format;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

import org.apache.commons.logging.Log;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Qualifier;
import org.springframework.jmx.export.annotation.ManagedOperation;
import org.springframework.jmx.export.annotation.ManagedResource;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.stereotype.Service;

import com.google.common.collect.Collections2;

/**
 * @author Alex Aiezza
 */
@Service
@ManagedResource
public class UserTracker
{
    private static final String LOGGED_IN  = "%s successfully Logged in!";

    private static final String LOGGED_OUT = "%s has Logged out!";

    private final Log           LOGGER;

    private final List<User>    onlineUsers;

    @Autowired
    private UserTracker( @Qualifier ( "UserTracker_Logger" ) final Log loginLogger )
    {
        LOGGER = loginLogger;
        onlineUsers = Collections.synchronizedList( new ArrayList<User>() );
    }


    synchronized void addUser( final User user )
    {
        if ( !onlineUsers.contains( user ) )
        {
            onlineUsers.add( user );
            LOGGER.info( format( LOGGED_IN, user ) );
        }
    }

    public synchronized boolean contains( final UserDetails user )
    {
        if ( user == null )
            return false;
        return onlineUsers.contains( user );
    }

    public synchronized List<User> getLoggedInUsers()
    {
        onlineUsers.forEach( ( user ) -> user.eraseCredentials() );
        return onlineUsers;
    }

    protected synchronized List<User> getPayload()
    {
        return getLoggedInUsers();
    }

    @ManagedOperation ( description = "View Active Users" )
    public List<String> getStringLoggedInUsers()
    {
        return new ArrayList<String>( Collections2.transform( getLoggedInUsers(),
            user -> user.getUsername() ) );
    }

    synchronized void removeUser( final User user )
    {
        onlineUsers.remove( user );
        LOGGER.info( format( LOGGED_OUT, user ) );
    }

    @Override
    public String toString()
    {
        return onlineUsers.toString();
    }
}
