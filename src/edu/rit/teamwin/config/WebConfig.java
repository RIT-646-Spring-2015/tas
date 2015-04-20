package edu.rit.teamwin.config;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.ComponentScan;
import org.springframework.context.annotation.Configuration;
import org.springframework.stereotype.Controller;
import org.springframework.web.servlet.config.annotation.EnableWebMvc;
import org.springframework.web.servlet.config.annotation.ResourceHandlerRegistry;
import org.springframework.web.servlet.config.annotation.WebMvcConfigurerAdapter;
import org.springframework.web.servlet.view.InternalResourceViewResolver;

import edu.rit.teamwin.web.TopicApprovalController;

/**
 * <p>
 * This configuration class is necessary for declaring resource handler mappings
 * for the Web Application as well as the internal resource view resolver.
 * </p>
 * <p>
 * The use of the {@link ComponentScan} annotation also allows for the automatic
 * instantiations of other Component beans. For instance, the application's
 * {@link Controller controllers}.
 * </p>
 * 
 * @author Alex Aiezza
 *
 */
@ComponentScan ( basePackageClasses = { TopicApprovalController.class } )
@Configuration
@EnableWebMvc
public class WebConfig extends WebMvcConfigurerAdapter
{
    private final Log LOG = LogFactory.getLog( getClass() );

    @Override
    public void addResourceHandlers( ResourceHandlerRegistry registry )
    {
        registry.addResourceHandler( "/css/**" ).addResourceLocations( "/css/" );
        registry.addResourceHandler( "/images/**" ).addResourceLocations( "/images/" );
        registry.addResourceHandler( "/js/**" ).addResourceLocations( "/js/" );
    }

    @Bean
    public InternalResourceViewResolver getInternalResourceViewResolver()
    {
        LOG.info( "IRVR" );
        final InternalResourceViewResolver irvr = new InternalResourceViewResolver();
        irvr.setPrefix( "/jsp/" );
        irvr.setSuffix( ".jsp" );
        return irvr;
    }
}
