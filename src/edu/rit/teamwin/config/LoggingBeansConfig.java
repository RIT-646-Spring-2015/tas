package edu.rit.teamwin.config;

import static org.apache.commons.logging.LogFactory.getLog;

import org.apache.commons.logging.Log;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

@Configuration
public class LoggingBeansConfig
{
    @Bean ( name = "Login_Logger" )
    public Log getLoginLogger()
    {
        return getLog( edu.rit.teamwin.web.LoginController.class );
    }

    @Bean ( name = "User_Logger" )
    public Log getUserLogger()
    {
        return getLog( edu.rit.teamwin.web.UserController.class );
    }

    @Bean ( name = "UserTracker_Logger" )
    public Log getUserTrackerLogger()
    {
        return getLog( edu.rit.teamwin.web.utils.UserTracker.class );
    }

}
