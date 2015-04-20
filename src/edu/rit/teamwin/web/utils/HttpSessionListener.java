package edu.rit.teamwin.web.utils;

import org.springframework.beans.BeansException;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.ApplicationContext;
import org.springframework.context.ApplicationListener;
import org.springframework.security.core.context.SecurityContext;
import org.springframework.security.core.session.SessionDestroyedEvent;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.web.context.WebApplicationContext;

//@Service
public class HttpSessionListener implements ApplicationListener<SessionDestroyedEvent>
{
    private final JdbcUserDetailsManager USER_MAN;

    private final UserTracker            USER_TRACKER;

    @Autowired
    public HttpSessionListener(
        final UserTracker userTracker,
        final JdbcUserDetailsManager userManager )
    {
        USER_TRACKER = userTracker;
        USER_MAN = userManager;
    }

    @Override
    public void onApplicationEvent( final SessionDestroyedEvent event )
    {
        for ( final SecurityContext securityContext : event.getSecurityContexts() )
        {
            final UserDetails ud = (UserDetails) securityContext.getAuthentication().getPrincipal();

            if ( USER_TRACKER.contains( ud ) )
            {
                final User user = USER_MAN.loadUserByUsername( ud.getUsername() );

                if ( user != null )
                    USER_TRACKER.removeUser( user );
            }
        }
    }

    public void setApplicationContext( final ApplicationContext applicationContext )
            throws BeansException
    {
        if ( applicationContext instanceof WebApplicationContext )
            ( (WebApplicationContext) applicationContext ).getServletContext().addListener( this );
        else // Either throw an exception or fail gracefully, up to you
        throw new RuntimeException( "Must be inside a web application context" );
    }
}
