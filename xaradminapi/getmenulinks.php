<?php
/**
 * Return the options for the admin menu
 *
 */

    function karma_adminapi_getmenulinks()
    {
        return xarModAPIFunc('base','admin','menuarray',array('module' => 'karma'));
    }

?>