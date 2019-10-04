<?php
/**
 * A class that contains code to handle any requests for the themes page
 *
 * @author Eddy Brown <e.a.m.brown@ncl.ac.uk>
 * @copyright 2016 Newcastle University
 *
 */
/**
 * Support /themes
 */
    class Themes extends Siteaction
    {
/**
 * Handle theme operations
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
        public function handle($context)
        {
            $fdt = $context->formdata();
            if ($fdt->haspost('theme'))
            {
                R::trashAll($context->user()->choices());

                foreach ($fdt->mustposta('theme') as $ix => $themeid)
                {
                    $choice = R::dispense('choice');
                    $choice->theme = $context->load('theme', $themeid);
                    $choice->user = $context->user();
                    $choice->rank = ($ix + 1);
                    R::store($choice);
                }
            }
            if ($fdt->haspost('themename'))
            {
                $theme = R::dispense('theme');
                $theme->name = $fdt->post('themename');

                $themestaff = R::dispense('themestaff');
                $themestaff->user = $context->load('user', $fdt->post('themeleader'));
                $themestaff->theme = $theme;
                R::store($theme);
                R::store($themestaff);

            }
            if ($fdt->haspost('delthemeid'))
            {
                $themeid = $fdt->post('delthemeid');

                R::trashAll(R::findAll('themestaff', 'theme_id = ?', [$themeid]));
                R::trashAll(R::findAll('finalchoice', 'theme_id = ?', [$themeid]));
                R::trashAll(R::findAll('choice', 'theme_id = ?', [$themeid]));

                $files = R::findAll('uploaddetail', 'theme_id = ?', [$themeid]);

                foreach ($files as $f)
                {
                    R::trash(R::findOne('uploaddetail', 'upload_id = ?', [$f->upload_id]));
                    R::trash(R::findOne('upload', 'id = ?', [$f->upload_id]));
                }

                R::trash(R::findOne('theme', 'id = ?', [$themeid]));
            }
            return 'themes.twig';
        }
    }
?>
