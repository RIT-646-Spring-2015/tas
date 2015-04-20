package edu.rit.teamwin.web.utils;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

import org.apache.commons.codec.digest.DigestUtils;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.jdbc.core.ResultSetExtractor;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.security.access.AccessDeniedException;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.AuthenticationException;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.core.userdetails.UserCache;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.security.core.userdetails.cache.NullUserCache;
import org.springframework.util.Assert;

/**
 * @author Alex Aiezza
 */
public class JdbcUserDetailsManager extends
org.springframework.security.provisioning.JdbcUserDetailsManager
{
    private class UserListExtractor implements ResultSetExtractor<List<User>>
    {
        private final UserMapper rowMapper;

        private final int        rowsExpected;

        public UserListExtractor()
        {
            this( new UserMapper(), 0 );
        }

        public UserListExtractor( final UserMapper rowMapper, final int rowsExpected )
        {
            Assert.notNull( rowMapper, "RowMapper is required" );
            this.rowMapper = rowMapper;
            this.rowsExpected = rowsExpected;
        }

        @Override
        public List<User> extractData( final ResultSet rs ) throws SQLException
        {
            final HashMap<String, User> results = rowsExpected > 0 ? new HashMap<String, User>(
                    rowsExpected ) : new HashMap<String, User>();
                    int rowNum = 0;
                    while ( rs.next() )
                    {
                        final User user = rowMapper.mapRow( rs, rowNum++ );

                        if ( results.containsKey( user.getUsername() ) )
                        {
                            final User inUser = results.get( user.getUsername() );
                            final ArrayList<GrantedAuthority> combinedAuthorities = new ArrayList<GrantedAuthority>();

                            combinedAuthorities.addAll( inUser.getAuthorities() );
                            combinedAuthorities.addAll( user.getAuthorities() );

                            results.put( user.getUsername(),
                                createUserDetails( user.getUsername(), user, combinedAuthorities ) );
                        } else results.put( user.getUsername(), user );
                    }

                    return new ArrayList<User>( results.values() );
        }
    }

    private class UserMapper implements RowMapper<User>
    {
        @Override
        public User mapRow( final ResultSet rs, final int rowNum ) throws SQLException
        {
            final Collection<SimpleGrantedAuthority> roles = new ArrayList<SimpleGrantedAuthority>();

            final String auths = rs.getString( "role" );

            roles.add( new SimpleGrantedAuthority( auths ) );

            final User user = new User( rs.getString( "username" ), rs.getString( "password" ),
                rs.getBoolean( "enabled" ), rs.getString( "first_name" ),
                rs.getString( "last_name" ), rs.getString( "email" ),
                rs.getString( "date_joined" ), rs.getString( "last_online" ), true, true, true,
                roles );

            return user;
        }
    }

    /**
     *
     */
    public final static String           QUERY_NUMBER_OF_USERS_SQL   = "SELECT COUNT(username) FROM users WHERE username = ?";

    /**
     *
     */
    public final static String           QUERY_USER_BY_USERNAME      = "SELECT users.username, password, enabled, first_name, last_name, email, date_joined, last_online, role FROM users LEFT JOIN user_role ON users.username=user_role.username WHERE users.username = ?";

    /**
     *
     */
    public final static String           NEW_USER_SQL                = "INSERT INTO users (username, password, enabled, first_name, last_name, email) VALUES ( ?, ?, ?, ?, ?, ? )";

    /**
     *
     */
    public final static String           NEW_USER_ROLE_SQL           = "INSERT INTO user_role (username, role) VALUES( ?, ? )";

    /**
     *
     */
    public final static String           DELETE_USER_SQL             = "DELETE FROM users WHERE username = ?";

    /**
     *
     */
    public final static String           DELETE_USER_ROLE_SQL        = "DELETE FROM user_role WHERE username = ?";

    /**
     *
     */
    public final static String           SELECT_ALL_USERS_SQL        = "SELECT users.username, password, enabled, first_name, last_name, email, date_joined, last_online, role FROM users LEFT JOIN user_role ON users.username=user_role.username";

    /**
     *
     */
    public final static String           SELECT_ALL_ROLES_SQL        = "SELECT role FROM roles";

    /**
     *
     */
    public final static String           DELETE_USER_AUTHORITIES_SQL = "delete from user_role where username = ?";

    /**
     *
     */
    public final static String           UPDATE_USER_SQL             = "UPDATE users SET enabled = ?, first_name = ?, last_name = ?, email = ? WHERE username = ?";

    /**
     *
     */
    public final static String           UPDATE_USER_LAST_ONLINE_SQL = "UPDATE users SET last_online = DATETIME('NOW', 'LOCALTIME') WHERE username = ?";

    public final static GrantedAuthority ADMIN                       = new SimpleGrantedAuthority(
            "ROLE_ADMIN" );

    public final static GrantedAuthority USER                        = new SimpleGrantedAuthority(
            "ROLE_USER" );

    public final static GrantedAuthority TEACHING_ASSISTANT          = new SimpleGrantedAuthority(
            "ROLE_TA" );

    private AuthenticationManager        authenticationManager;

    private final UserCache              userCache                   = new NullUserCache();

    private final boolean                NEW_USER_ENABLED;

    @Autowired
    private JdbcUserDetailsManager( @Value ( "${newUserEnabled}" ) final boolean newUserEnabled )
    {
        NEW_USER_ENABLED = newUserEnabled;
    }

    @Override
    public void changePassword( final String oldPassword, final String newPassword )
            throws AuthenticationException
    {
        final Authentication currentUser = SecurityContextHolder.getContext().getAuthentication();

        if ( currentUser == null )
            // This would indicate bad coding somewhere
            throw new AccessDeniedException(
                "Can't change password as no Authentication object found in context "
                        + "for current user." );

        final String username = currentUser.getName();

        // If an authentication manager has been set, re-authenticate the user
        // with the supplied password.
        if ( authenticationManager != null )
        {
            logger.debug( "Reauthenticating user '" + username + "' for password change request." );

            authenticationManager.authenticate( new UsernamePasswordAuthenticationToken( username,
                DigestUtils.sha1Hex( oldPassword ) ) );
        } else logger.debug( "No authentication manager set. Password won't be re-checked." );

        logger.debug( "Changing password for user '" + username + "'" );

        getJdbcTemplate().update( DEF_CHANGE_PASSWORD_SQL, DigestUtils.sha1Hex( newPassword ),
            username );

        SecurityContextHolder.getContext().setAuthentication(
            createNewAuthentication( currentUser, newPassword ) );

        userCache.removeUserFromCache( username );
    }

    public void changePassword(
            final String username,
            final String oldPassword,
            final String newPassword ) throws AuthenticationException
    {
        final Authentication currentUser = SecurityContextHolder.getContext().getAuthentication();

        if ( currentUser == null )
            // This would indicate bad coding somewhere
            throw new AccessDeniedException(
                "Can't change password as no Authentication object found in context "
                        + "for current user." );

        if ( currentUser.getName().equals( username ) )
        {
            changePassword( oldPassword, newPassword );
            return;
        }

        if ( !currentUser.getAuthorities().contains( ADMIN ) )
            throw new AccessDeniedException(
                    "Only Administrators can change the password of another user" );

        // If an authentication manager has been set, re-authenticate the user
        // with the supplied password.
        if ( authenticationManager != null )
        {
            logger.debug( "Reauthenticating user '" + currentUser.getName() +
                    "' for password change request." );

            authenticationManager.authenticate( new UsernamePasswordAuthenticationToken(
                currentUser.getName(), oldPassword ) );
        } else logger.debug( "No authentication manager set. Password won't be re-checked." );

        logger.debug( "Changing password for user '" + username + "'" );

        getJdbcTemplate().update( DEF_CHANGE_PASSWORD_SQL, DigestUtils.sha1Hex( newPassword ),
            username );

        userCache.removeUserFromCache( username );
    }

    public UserDetails checkForAdminRights() throws IllegalAccessException
    {
        final User user = getUser();

        if ( user == null || !user.isEnabled() || !user.getAuthorities().contains( ADMIN ) )
            throw new IllegalAccessException( "INVALID CREDENTIALS" );

        return user;
    }

    public boolean checkForAdminRights( final User user )
    {
        return user != null && user.getAuthorities().contains( ADMIN );
    }

    /**
     * @param user
     */
    public void createUser( final UserForm user )
    {
        getJdbcTemplate().update( NEW_USER_SQL, ps -> {
            ps.setString( 1, user.getUsername() );
            ps.setString( 2, DigestUtils.sha1Hex( user.getPassword() ) );
            ps.setBoolean( 3, NEW_USER_ENABLED ); // Changing this so admin
            // has to enable
            // people first!
            ps.setString( 4, user.getFirst_name() );
            ps.setString( 5, user.getLast_name() );
            ps.setString( 6, user.getEmail() );
        } );

        if ( getEnableAuthorities() )
            getJdbcTemplate().update( NEW_USER_ROLE_SQL, user.getUsername(), USER.getAuthority() );

    }

    protected User createUserDetails(
            final String username,
            final User userFromUserQuery,
            final List<GrantedAuthority> combinedAuthorities )
    {
        String returnUsername = userFromUserQuery.getUsername();

        if ( !isUsernameBasedPrimaryKey() )
            returnUsername = username;

        return new User( returnUsername, userFromUserQuery.getPassword(),
            userFromUserQuery.isEnabled(), userFromUserQuery.getFirst_name(),
            userFromUserQuery.getLast_name(), userFromUserQuery.getEmail(),
            userFromUserQuery.getDate_joined(), userFromUserQuery.getLast_online(), true, true,
            true, combinedAuthorities );
    }

    /**
     * @see org.springframework.security.provisioning.JdbcUserDetailsManager#deleteUser(java.lang.String)
     */
    @Override
    public void deleteUser( final String username )
    {
        try
        {
            if ( checkForAdminRights( loadUserByUsername( username ) ) )
                return;

            checkForAdminRights();

            if ( getEnableAuthorities() )
                deleteUserAuthorities( username );
            getJdbcTemplate().update( DELETE_USER_SQL, username );
            userCache.removeUserFromCache( username );
        } catch ( final IllegalAccessException e )
        {
            e.printStackTrace();
        }
    }

    /**
     * @param username
     */
    private void deleteUserAuthorities( final String username )
    {
        getJdbcTemplate().update( DELETE_USER_AUTHORITIES_SQL, username );
    }

    public List<String> getAvailableAuthorities()
    {
        return getJdbcTemplate().queryForList( SELECT_ALL_ROLES_SQL, String.class );
    }

    public User getUser()
    {
        final Authentication auth = SecurityContextHolder.getContext().getAuthentication();

        User user = null;
        if ( auth != null && auth.getPrincipal() instanceof UserDetails )
            user = loadUserByUsername( ( (UserDetails) auth.getPrincipal() ).getUsername() );

        return user;
    }

    public List<User> getUsers()
    {
        final List<User> users = getJdbcTemplate().query( SELECT_ALL_USERS_SQL,
            new UserListExtractor() );

        return users;
    }

    /**
     * @param user
     */
    private void insertUserAuthorities( final User user, final GrantedAuthority auth )
    {
        getJdbcTemplate().update( NEW_USER_ROLE_SQL, user.getUsername(), auth.getAuthority() );
    }

    /**
     * Executes the SQL <tt>usersByUsernameQuery</tt> and returns a list of
     * UserDetails objects. There should normally only be one matching user.
     */
    protected List<User> loadTheUsersByUsername( final String username )
    {
        return getJdbcTemplate().query( QUERY_USER_BY_USERNAME, new String [] { username },
            new UserMapper() );
    }

    @Override
    public User loadUserByUsername( final String username ) throws UsernameNotFoundException
    {
        final List<User> users = getJdbcTemplate().query( QUERY_USER_BY_USERNAME,
            new String [] { username }, new UserMapper() );

        if ( users.size() == 0 )
        {
            logger.debug( "Query returned no results for user '" + username + "'" );

            throw new UsernameNotFoundException( messages.getMessage( "JdbcDaoImpl.notFound",
                new Object [] { username }, String.format( "Username %s not found", username ) ) );
        }

        final User user = users.get( 0 ); // contains no GrantedAuthority[]

        final Set<GrantedAuthority> dbAuthsSet = new HashSet<GrantedAuthority>();

        if ( getEnableAuthorities() )
            dbAuthsSet.addAll( loadUserAuthorities( user.getUsername() ) );

        if ( getEnableGroups() )
            dbAuthsSet.addAll( loadGroupAuthorities( user.getUsername() ) );

        final List<GrantedAuthority> dbAuths = new ArrayList<GrantedAuthority>( dbAuthsSet );

        addCustomAuthorities( user.getUsername(), dbAuths );

        if ( dbAuths.size() == 0 )
        {
            logger.debug( "User '" + username +
                    "' has no authorities and will be treated as 'not found'" );

            throw new UsernameNotFoundException( messages.getMessage( "JdbcDaoImpl.noAuthority",
                new Object [] { username },
                String.format( "User %s has no GrantedAuthority", username ) ) );
        }

        return createUserDetails( username, user, dbAuths );
    }

    public void loggedIn( final User user )
    {
        getJdbcTemplate().update( UPDATE_USER_LAST_ONLINE_SQL, ps -> {
            ps.setString( 1, user.getUsername() );
        } );
    }

    /**
     * @param user
     */
    public void updateUser( final User user )
    {
        try
        {
            if ( !getUser().getUsername().equals( user.getUsername() ) )
                checkForAdminRights();

            getJdbcTemplate().update( UPDATE_USER_SQL, ps -> {
                ps.setBoolean( 1, user.isEnabled() );
                ps.setString( 2, user.getFirst_name() );
                ps.setString( 3, user.getLast_name() );
                ps.setString( 4, user.getEmail() );
                ps.setString( 5, user.getUsername() );
            } );

            if ( getEnableAuthorities() )
            {

                deleteUserAuthorities( user.getUsername() );
                for ( final GrantedAuthority auth : user.getAuthorities() )
                    insertUserAuthorities( user, auth );
            }

            userCache.removeUserFromCache( user.getUsername() );
        } catch ( final IllegalAccessException e )
        {
            e.printStackTrace();
        }
    }
}
