package edu.rit.teamwin.config;

import javax.servlet.ServletContext;
import javax.servlet.ServletException;
import javax.servlet.ServletRegistration;

import org.springframework.web.WebApplicationInitializer;
import org.springframework.web.context.ContextLoaderListener;
import org.springframework.web.context.support.AnnotationConfigWebApplicationContext;
import org.springframework.web.servlet.DispatcherServlet;
import org.springframework.web.util.Log4jConfigListener;

/**
 * This class implements {@link WebApplicationInitializer}, which is auto
 * detected by Spring on server startup. This class's
 * {@link WebApplicationInitializer#onStartup(ServletContext) onStartup} method
 * essentially replaces the conventional <tt>web.xml</tt> file.
 * 
 * @author Alex Aiezza
 *
 */
public class WebInit implements WebApplicationInitializer
{

    @Override
    public void onStartup( final ServletContext container ) throws ServletException
    {
        // Establishes logging
        container.setInitParameter( "log4jConfigLocation", "/resources/log4j.xml" );
        container.addListener( Log4jConfigListener.class );

        // Creates the root application context
        final AnnotationConfigWebApplicationContext appContext = new AnnotationConfigWebApplicationContext();

        appContext.setDisplayName( "Topic Approval System" );
        appContext.register( AppConfig.class );
        container.addListener( new ContextLoaderListener( appContext ) );

        // Creates the dispatcher servlet context
        final AnnotationConfigWebApplicationContext servletContext = new AnnotationConfigWebApplicationContext();

        // Registers the servlet configuraton with the dispatcher servlet
        // context
        servletContext.register( ServletConfig.class );

        // Further configures the servlet context
        ServletRegistration.Dynamic dispatcher = container.addServlet( "tas-dispatcher",
            new DispatcherServlet( servletContext ) );
        dispatcher.setLoadOnStartup( 1 );
        dispatcher.addMapping( "/" );
    }
}
