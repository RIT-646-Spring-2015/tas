package edu.rit.teamwin.web;

import static edu.rit.teamwin.web.utils.JdbcUserDetailsManager.ADMIN;
import static org.springframework.web.bind.annotation.RequestMethod.GET;
import static org.springframework.web.bind.annotation.RequestMethod.POST;

import java.io.UnsupportedEncodingException;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

import org.apache.commons.codec.digest.DigestUtils;
import org.apache.commons.logging.Log;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Qualifier;
import org.springframework.http.HttpStatus;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.stereotype.Controller;
import org.springframework.validation.BindingResult;
import org.springframework.validation.ObjectError;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.ResponseBody;
import org.springframework.web.servlet.ModelAndView;

import edu.rit.teamwin.web.utils.JdbcUserDetailsManager;
import edu.rit.teamwin.web.utils.SignUpFormValidator;
import edu.rit.teamwin.web.utils.User;
import edu.rit.teamwin.web.utils.UserForm;
import edu.rit.teamwin.web.utils.UserTracker;

/**
 * @author Alex Aiezza
 */
@Controller
public class UserController
{

    private final static String          UNFINISHED_SIGN_UP_FORM = "_unifinshedSignUpForm_";

    private final Log                    LOGGER;

    private final JdbcUserDetailsManager USER_MAN;

    private final UserTracker            USER_TRACKER;

    @Autowired
    public UserController(
            @Qualifier ( "User_Logger" ) final Log logger,
            final JdbcUserDetailsManager userMan,
            final UserTracker userTracker )
    {
        LOGGER = logger;
        USER_MAN = userMan;
        USER_TRACKER = userTracker;
    }

    private String createNewUser( final UserForm user )
    {
        // ADD new user to database
        try
        {
            USER_MAN.createUser( user );
        } catch ( final Exception e )
        {
            LOGGER.error( e.getMessage() );
            return e.getMessage();
        }

        return null;
    }

    @RequestMapping ( method = POST, value = "deleteUser/{someusername}" )
    private void deleteUser(
            @PathVariable ( "someusername" ) final String username,
            final HttpServletResponse response ) throws UnsupportedEncodingException
    {
        USER_MAN.deleteUser( username );

        LOGGER.info( String.format( "USER :%s: has been DELETED! ", username ) );

        response.setStatus( HttpStatus.OK.value() );
    }

    @RequestMapping ( method = GET, value = "whoAmI" )
    @ResponseBody
    public String getCurrentUser()
    {
        return USER_MAN.getUser().getUsername();
    }

    @RequestMapping ( method = GET, value = "/signup" )
    public ModelAndView getSignupPage()
    {
        if ( USER_TRACKER.contains( USER_MAN.getUser() ) )
            return new ModelAndView( "redirect:profile" );
        return new ModelAndView( "signup" );
    }

    @RequestMapping ( method = POST, value = "getUser/{someusername}" )
    @ResponseBody
    public HashMap<String, Object> getUser( @PathVariable ( "someusername" ) final String username )
            throws SQLException, UnsupportedEncodingException
            {
        if ( !USER_MAN.userExists( username ) )
            return null;
        final HashMap<String, Object> json = new HashMap<String, Object>();

        json.put( "user", USER_MAN.loadUserByUsername( username ) );

        final User currentUser = USER_MAN.getUser();

        final boolean admin = currentUser.getAuthorities().contains( ADMIN );

        json.put( "isAdmin", admin );

        return json;
            }

    @RequestMapping ( method = GET, value = "userManagement" )
    public ModelAndView getUserManagementPage() throws SQLException
    {
        return new ModelAndView( "userManagement" );
    }

    @RequestMapping ( method = POST, value = "retrieveUsers" )
    @ResponseBody
    public Collection<User> getUsers() throws SQLException, IllegalAccessException
    {
        return USER_MAN.getUsers();
    }

    @ModelAttribute ( "newUserForm" )
    public UserForm populateNewUserForm( final UserForm user, final HttpSession session )
    {
        final UserForm form = (UserForm) session.getAttribute( UNFINISHED_SIGN_UP_FORM );

        if ( !user.isBlank() )
        {
            user.clearPassword();
            return user;
        }

        if ( form != null )
        {
            form.clearPassword();
            return form;
        }

        return new UserForm();
    }

    @RequestMapping ( method = POST, value = "signup" )
    public ModelAndView signup(
            final UserForm form,
            final BindingResult result,
            final HttpServletRequest request )
    {
        // VALIDATE THE FORM
        final SignUpFormValidator suValidator = new SignUpFormValidator();

        if ( !form.isBlank() )
            request.getSession().setAttribute( UNFINISHED_SIGN_UP_FORM, form );

        suValidator.validate( form, result );

        if ( USER_MAN.userExists( form.getUsername() ) )
            result.rejectValue( "username", "Username is already taken" );

        // PRINT OUT ALL ERRORS IF ANY
        if ( result.hasErrors() )
        {
            final StringBuilder out = new StringBuilder();

            for ( final ObjectError er : result.getAllErrors() )
                out.append( er.getCode() ).append( "<br>" );

            return new ModelAndView( "signup", "message", out );
        }

        // IF NO ERRORS, CREATE AND AUTHENTICATE USER
        final String message = createNewUser( form );

        if ( message != null )
            return new ModelAndView( "signup", "message", message );

        LOGGER.info( String.format( "FRESH MEAT -> %s", form ) );

        return new ModelAndView( "redirect:login", "message", message );
    }

    @RequestMapping ( method = POST, value = "updateUser" )
    private ModelAndView updateUser(
            final UserForm userToUpdate,
            @RequestParam ( value = "auths" ) final ArrayList<SimpleGrantedAuthority> authorities,
            final BindingResult result )
    {
        userToUpdate.setAuthorities( authorities );

        if ( !USER_MAN.userExists( userToUpdate.getUsername() ) )
            result.rejectValue( "username", "Username does not exist" );

        final User shabaUser = USER_MAN.loadUserByUsername( userToUpdate.getUsername() );

        final SignUpFormValidator suValidator = new SignUpFormValidator();

        if ( userToUpdate.getConfirmPassword().isEmpty() )
        {
            suValidator.validateRequiredFields( userToUpdate, result );

            if ( !DigestUtils.sha1Hex( userToUpdate.getPassword() )
                    .equals( shabaUser.getPassword() ) &&
                    !USER_MAN.checkForAdminRights( USER_MAN.getUser() ) )
                result.rejectValue( "password", "Wrong Password" );

        } else suValidator.validate( userToUpdate, result );

        // PRINT OUT ALL ERRORS IF ANY
        if ( result.hasErrors() )
        {
            final StringBuilder out = new StringBuilder();

            for ( final ObjectError er : result.getAllErrors() )
                out.append( er.getCode() ).append( "<br>" );

            return new ModelAndView( "redirect:userDetails/" + userToUpdate.getUsername(),
                "message", out );
        }

        if ( userToUpdate.getConfirmPassword().isEmpty() )
            USER_MAN.updateUser( User.ShabaUserFromForm( userToUpdate ) );
        else USER_MAN.changePassword( userToUpdate.getUsername(), shabaUser.getPassword(),
            userToUpdate.getPassword() );

        final String message = String.format( "%s UPDATED!",
            USER_MAN.loadUserByUsername( shabaUser.getUsername() ) );

        LOGGER.info( message );

        return new ModelAndView( "redirect:userDetails/" + userToUpdate.getUsername(), "message",
            message );
    }

    @RequestMapping ( method = GET, value = "userDetails/{user}" )
    private ModelAndView userDetails( @PathVariable final String user, @RequestParam (
        required = false ) final String message ) throws UnsupportedEncodingException
    {
        final HashMap<String, Object> map = new HashMap<String, Object>();

        final User currentUser = USER_MAN.getUser();

        final boolean admin = currentUser.getAuthorities().contains( ADMIN );

        if ( !USER_MAN.userExists( user ) || !currentUser.equals( user ) && !admin )
            return new ModelAndView( String.format( "redirect:%s", currentUser.getUsername() ) );

        map.put( "admin", admin );

        map.put( "user", user );

        map.put( "availableAuthorities", USER_MAN.getAvailableAuthorities() );

        map.put( "userAuths", USER_MAN.loadUserByUsername( user ).getAuthorities() );

        if ( message != null )
            map.put( "message", message );

        return new ModelAndView( "userDetails", map );
    }
}
