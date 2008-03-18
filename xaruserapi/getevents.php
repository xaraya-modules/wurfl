<?php

    sys::import('modules.query.class.query');

    function calendar_userapi_getevents($args)
    {
        extract($args);
        $xartable = xarDB::getTables();

        $q = new Query('SELECT');
        $q->addtable($xartable['calendar_event']);
        $q->ge('start',$day->thisDay(TRUE));
        $q->lt('start',$day->nextDay(TRUE));

        if (!$q->run()) return;
        return $q->output();
    }

?>
