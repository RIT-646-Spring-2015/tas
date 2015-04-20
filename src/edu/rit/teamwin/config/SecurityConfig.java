package edu.rit.teamwin.config;

import org.springframework.context.annotation.Configuration;
import org.springframework.context.annotation.ImportResource;
import org.springframework.security.config.annotation.web.configuration.EnableWebSecurity;
import org.springframework.security.config.annotation.web.configuration.WebSecurityConfigurerAdapter;

@Configuration
@EnableWebSecurity
@ImportResource ( "WEB-INF/spring/security-config.xml" )
public class SecurityConfig extends WebSecurityConfigurerAdapter
{

    // @Autowired
    // private DataSource dataSource;

    // @Override
    // protected void configure( AuthenticationManagerBuilder auth ) throws
    // Exception
    // {
    // auth.jdbcAuthentication()
    // .dataSource( dataSource )
    // .authoritiesByUsernameQuery(
    // "select username,role from user_role where username = ?" );
    // }
}
