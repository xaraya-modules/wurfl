<?php

function calendar_admin_main()
{
    if(!xarSecurityCheck('AdminCalendar')) return;

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return array();
    } else {
       xarController::redirect(xarModURL('calendar','admin', 'view'));
    }

    return true;
}
?>

