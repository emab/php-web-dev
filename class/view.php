<?php
/**
 * A class that contains code to handle any requests for the view page
 *
 * @author Eddy Brown <e.a.m.brown@ncl.ac.uk>
 * @copyright 2016 Newcastle University
 *
 */
/**
 * Support /view
 */
    class View extends Siteaction
    {
/**
 * Handle view operations
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
        public function handle($context)
        {
            return 'view.twig';
        }
    }
?>
