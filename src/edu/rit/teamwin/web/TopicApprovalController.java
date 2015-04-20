package edu.rit.teamwin.web;

import javax.servlet.http.HttpServletRequest;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.servlet.ModelAndView;

/**
 * @author Alex Aiezza
 *
 */
@Controller
public class TopicApprovalController
{
    private final Log LOG = LogFactory.getLog( getClass() );

    @RequestMapping ( value = "/", method = RequestMethod.GET )
    public ModelAndView sayHello( final HttpServletRequest request )
    {
        LOG.info( "hello!" );

        final ModelAndView model = new ModelAndView( "index" );
        return model;
    }
}
