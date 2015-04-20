package edu.rit.teamwin.config;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.ComponentScan;
import org.springframework.context.annotation.ComponentScan.Filter;
import org.springframework.context.annotation.Configuration;
import org.springframework.context.annotation.FilterType;
import org.springframework.context.annotation.Import;
import org.springframework.context.annotation.PropertySource;
import org.springframework.context.support.PropertySourcesPlaceholderConfigurer;
import org.springframework.core.io.FileSystemResource;
import org.springframework.core.io.Resource;
import org.springframework.stereotype.Controller;

import edu.rit.teamwin.web.utils.UserTracker;


/**
 * This class is empty but essentially replaces the conventional
 * <tt>root-context.xml</tt> file.
 *
 * @author Alex Aiezza
 *
 */
@PropertySource ( "resources/topic-selection.properties" )
@ComponentScan ( basePackageClasses = UserTracker.class, excludeFilters = { @Filter (
    type = FilterType.ANNOTATION,
    value = Controller.class ) } )
@Import ( { LoggingBeansConfig.class, DatabaseConfig.class, SecurityConfig.class } )
@Configuration
public class AppConfig
{
    @Bean
    public static PropertySourcesPlaceholderConfigurer propertySourcesPlaceholderConfigurer()
    {
        final PropertySourcesPlaceholderConfigurer pspc = new PropertySourcesPlaceholderConfigurer();
        final Resource [] resources = new FileSystemResource [] { new FileSystemResource(
                "resources/topic-selection.properties" ) };
        pspc.setLocations( resources );
        pspc.setIgnoreUnresolvablePlaceholders( true );
        return pspc;
    }
}
