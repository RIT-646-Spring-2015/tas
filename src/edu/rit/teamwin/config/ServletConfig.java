package edu.rit.teamwin.config;

import org.springframework.context.annotation.Configuration;
import org.springframework.context.annotation.Import;

/**
 * Similarly to {@link AppConfig}, this class replaces the conventional
 * <tt>servlet-context.xml</tt> file.
 * 
 * @author Alex Aiezza
 *
 */
@Configuration
@Import ( WebConfig.class )
public class ServletConfig
{

}
