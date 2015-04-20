package edu.rit.teamwin.web.utils;

import java.io.IOException;

import javax.servlet.FilterChain;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.web.authentication.UsernamePasswordAuthenticationFilter;

public class TrackingUsernamePasswordAuthenticationFilter extends
UsernamePasswordAuthenticationFilter
{
    private final JdbcUserDetailsManager USER_MAN;

    private final UserTracker            USER_TRACKER;

    @Autowired
    private TrackingUsernamePasswordAuthenticationFilter(
            final UserTracker userTracker,
            final JdbcUserDetailsManager userManager )
    {
        USER_TRACKER = userTracker;
        USER_MAN = userManager;
    }

    /**
     * @see org.springframework.security.web.authentication.AbstractAuthenticationProcessingFilter#successfulAuthentication(javax.servlet.http.HttpServletRequest,
     *      javax.servlet.http.HttpServletResponse, javax.servlet.FilterChain,
     *      org.springframework.security.core.Authentication)
     */
    @Override
    protected void successfulAuthentication(
            final HttpServletRequest request,
            final HttpServletResponse response,
            final FilterChain chain,
            final Authentication authResult ) throws IOException, ServletException
    {
        super.successfulAuthentication( request, response, chain, authResult );
        final User user = USER_MAN.loadUserByUsername( ( (UserDetails) authResult.getPrincipal() )
            .getUsername() );
        USER_TRACKER.addUser( user );
        USER_MAN.loggedIn( user );
    }
}
