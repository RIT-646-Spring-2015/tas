package edu.rit.teamwin.web;

import static edu.rit.teamwin.web.utils.JdbcUserDetailsManager.ADMIN;
import static edu.rit.teamwin.web.utils.JdbcUserDetailsManager.USER;

import java.util.HashMap;

import javax.servlet.http.HttpServletRequest;

import org.apache.commons.logging.Log;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Qualifier;
import org.springframework.security.authentication.BadCredentialsException;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.ModelAndView;

import edu.rit.teamwin.web.utils.JdbcUserDetailsManager;
import edu.rit.teamwin.web.utils.User;
import edu.rit.teamwin.web.utils.UserTracker;

/**
 * @author Alex Aiezza
 */
@Controller
public class LoginController
{
    private final UserTracker            USER_TRACKER;

    private final JdbcUserDetailsManager USER_MAN;

    private final Log                    LOGGER;

    @Autowired
    public LoginController(
            @Qualifier ( "Login_Logger" ) final Log logger,
            final UserTracker userTracker,
            final JdbcUserDetailsManager userManager )
    {
        LOGGER = logger;
        USER_TRACKER = userTracker;
        USER_MAN = userManager;
    }

    @RequestMapping ( "/*" )
    public ModelAndView backToYourRoots()
    {
        return new ModelAndView( "redirect:login" );
    }

    // customize the error message
    private String getErrorMessage( final HttpServletRequest request, final String key )
    {

        final Exception exception = (Exception) request.getSession().getAttribute( key );

        String error = "";
        if ( exception instanceof BadCredentialsException )
            error = "Invalid username and password!";
        else error = exception.getMessage();

        if ( error.equals( "User is disabled" ) )
            error += "\nPlease Await Administrator Approval!";

        return error;
    }

    @RequestMapping ( "login" )
    public ModelAndView getLoginForm(
            @RequestParam ( required = false ) final String authfailed,
            final String logout,
            final HttpServletRequest request )
    {
        if ( USER_TRACKER.contains( USER_MAN.getUser() ) )
            return new ModelAndView( "redirect:profile" );

        final HashMap<String, Object> map = new HashMap<String, Object>();

        String message = "";
        if ( authfailed != null )
        {

            message = getErrorMessage( request, "SPRING_SECURITY_LAST_EXCEPTION" );
            map.put( "success", false );
        } else if ( logout != null )
        {
            message = "Logged Out successfully!";
            map.put( "success", true );
        }

        if ( message.isEmpty() )
            LOGGER.debug( "Waiting For Login" );

        map.put( "message", message );

        return new ModelAndView( "login", map );
    }

    @RequestMapping ( "profile" )
    public ModelAndView getProfilePage()
    {
        final User user = USER_MAN.getUser();

        final HashMap<String, Object> map = new HashMap<String, Object>();

        map.put( "username", user.getUsername() );

        map.put( "admin", user.getAuthorities().contains( ADMIN ) );
        map.put( "user", user.getAuthorities().contains( USER ) );

        return new ModelAndView( "profile", map );
    }
}
