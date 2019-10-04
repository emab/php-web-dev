<?php
/**
 * A class that contains code to handle any requests for the myuploads page
 *
 * @author Eddy Brown <e.a.m.brown@ncl.ac.uk>
 * @copyright 2016 Newcastle University
 *
 */
/**
 * Support the /myuploads page
 */
    class MyUploads extends Siteaction
    {
/**
 * Handle /myuploads
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
        public function handle($context)
        {
            $fdt = $context->formdata();
            if ($fdt->haspost('hideshowfile'))
            {
                $file = R::findOne('uploaddetail', 'upload_id = ?', [$fdt->post('hideshowfile')]);
                if ($file->hidden == 'on')
                {
                    $file->hidden = "";
                    R::store($file);
                }
                else
                {
                    $file->hidden = "on";
                    R::store($file);
                }
            }
            if ($fdt->haspost('deletefile'))
            {
                $fileinfo = R::findOne('uploaddetail', 'upload_id = ?', [$fdt->post('deletefile')]);
                $file = R::findOne('upload', 'id = ?', [$fdt->post('deletefile')]);
                R::trash($fileinfo);
                R::trash($file);
            }
            return 'myuploads.twig';
        }
    }
?>
