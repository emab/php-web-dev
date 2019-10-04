<?php
/**
 * A class that contains code to handle any requests for the themelist page
 *
 * @author Eddy Brown <e.a.m.brown@ncl.ac.uk>
 * @copyright 2016 Newcastle University
 *
 */
/**
 * Support the /themelist page
 */
    class ThemeList extends Siteaction
    {
/**
 * Handle themelist operation
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
        public function handle($context)
        {
            return 'themelist.twig';
        }
    }
?>
