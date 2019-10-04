<?php
/**
 * A class that contains code to handle any requests for the selection page
 *
 * @author Eddy Brown <e.a.m.brown@ncl.ac.uk>
 * @copyright 2016 Newcastle University
 *
 */
/**
 * Support /selection
 */
    class Selection extends Siteaction
    {
/**
 * Handle selection operations
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
        public function handle($context)
        {

            $fdt = $context->formdata();

            if ($fdt->haspost('choice'))
            {
                $userid = $fdt->post('userid');
                $themeid = $fdt->post('choice');

                $finaltheme = R::dispense('finalchoice');
                $finaltheme->theme = $context->load('theme', $themeid);
                $finaltheme->user = $context->load('user', $userid);
                R::store($finaltheme);

                // mail($context->getUserEmail($userid), 'Theme Allocation', 'You have been allocated the theme '
                //     + $context->getThemeName($themeid));
            }
            if ($fdt->haspost('supervisor'))
            {
                $userid = $fdt->post('userid');
                $supervisorid = $fdt->post('supervisor');

                $supervisor = R::findOne('finalchoice', 'user_id = ?', [$userid]);
                $supervisor->supervisor = $context->load('user', $supervisorid);
                R::store($supervisor);

                // mail($context->getUserEmail($userid), 'Supervisor Allocation', 'You have been allocated '
                //     + $context->getUserLogin($supervisorid) + ' and their contact address is '
                //     + $context->getUserEmail($supervisorid));
                //
                // mail($context->getUserEmail($supervisor), 'Supervisor Allocation', 'You have been allocated '
                //     + $context->getUserLogin($userid) + ' and their contact address is '
                //     + $context->getUserEmail($userid));
            }

            return 'selection.twig';
        }
    }
?>
