<?php
/**
 * A class that contains code to implement a profile page
 *
 * @author Lindsay Marshall <lindsay.marshall@ncl.ac.uk>
 * @copyright 2016 Newcastle University
 *
 */
/**
 * Support the /profile page
 */
    class Profile extends Siteaction
    {
/**
 * Handle profile operations /profile/xxxx
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
        public function handle($context)
        {
            $formd = $context->formdata();
            if (($email = $formd->post('email', '')) !== '')
            { # there is a post
	           $context->local()->addval('done', TRUE);
            }
            return 'profile.twig';
        }
    }
?>
