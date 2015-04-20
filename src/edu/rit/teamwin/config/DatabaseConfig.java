package edu.rit.teamwin.config;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.jdbc.datasource.DriverManagerDataSource;

@Configuration
public class DatabaseConfig
{
    @Bean ( name = "dataSource" )
    DriverManagerDataSource getDataSource(
            @Value ( "${db.driver}" ) final String driver,
            @Value ( "${db.url}" ) final String url,
            @Value ( "${db.username}" ) final String username,
            @Value ( "${db.password}" ) final String password )
    {
        final DriverManagerDataSource dmds = new DriverManagerDataSource( url, username, password );
        dmds.setDriverClassName( driver );

        return dmds;
    }

}
