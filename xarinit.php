<?php
/**
 *  Module Initialisation Function
 *  @version $Id: xarinit.php,v 1.7 2003/06/24 20:08:10 roger Exp $
 *  @author Roger Raymond, Andrea Moro
 *  @todo determine DB Table schema
 *  @todo determine all module vars
 *  @todo determine permissions masks
 *  @todo determine blocklayout tags
 */
function calendar_init()
{
# --------------------------------------------------------
#
# Set up tables
#
    $q = new xarQuery();
    $prefix = xarDBGetSiteTablePrefix();

    $query = "DROP TABLE IF EXISTS " . $prefix . "_calendar_calendar";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_calendar_calendar (
      id          int(10) unsigned NOT NULL auto_increment,
      itemid      int(11) unsigned default null,
      itemtype    int(11) unsigned default null,
      modid       int(11) unsigned default null,
      name        varchar(60) default '' NOT NULL,
      description text,
    PRIMARY KEY  (id)
    ) TYPE=MyISAM";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_calendar_event";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_calendar_event (
      id              int NOT NULL auto_increment,
      itemtype        int(4) NULL,
      name            varchar(80) DEFAULT '' NOT NULL,
      description     text,
      start           int(11) NULL,
      end             int(11) NULL,
      recurring       int(4) default 0 NOT NULL,
      start_location  varchar(20) NULL,
      end_location    varchar(20) NULL,
      objectid        int(4) NULL,
      owner           int(11) NULL,
      status          int(4) default 0 NOT NULL,
      timestamp       int(11) default 0 NOT NULL,

      PRIMARY KEY (id),
      KEY i_start (start),
      KEY i_end   (end)
    ) TYPE=MyISAM";
    if (!$q->run($query)) return;

/*    $query = "DROP TABLE IF EXISTS " . $prefix . "_bookings_repeat";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_bookings_repeat (
      id          int NOT NULL auto_increment,
      start_time  int DEFAULT '0' NOT NULL,
      end_time    int DEFAULT '0' NOT NULL,
      rep_type    int DEFAULT '0' NOT NULL,
      end_date    int DEFAULT '0' NOT NULL,
      rep_opt     varchar(32) DEFAULT '' NOT NULL,
      objectid     int DEFAULT '1' NOT NULL,
      timestamp int(11) default 0 NOT NULL,
      owner int(11) default 0 NOT NULL,
      name        varchar(80) DEFAULT '' NOT NULL,
      status int(11) default 0 NOT NULL,
      description text,
      rep_num_weeks smallint NULL,

      PRIMARY KEY (id)
    ) TYPE=MyISAM";
    if (!$q->run($query)) return;
*/

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewCalendar','All','calendar','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadCalendar','All','calendar','All','All','ACCESS_READ');
    xarRegisterMask('CommentCalendar','All','calendar','All','All','ACCESS_COMMENT');
    xarRegisterMask('ModerateCalendar','All','calendar','All','All','ACCESS_MODERATE');
    xarRegisterMask('EditCalendar','All','calendar','All','All','ACCESS_EDIT');
    xarRegisterMask('AddCalendar','All','calendar','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteCalendar','All','calendar','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminCalendar','All','calendar','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('ViewCalendar','All','calendar','All','All','ACCESS_VIEW');
    xarRegisterPrivilege('ReadCalendar','All','calendar','All','All','ACCESS_READ');
    xarRegisterPrivilege('CommentCalendar','All','calendar','All','All','ACCESS_COMMENT');
    xarRegisterPrivilege('ModerateCalendar','All','calendar','All','All','ACCESS_MODERATE');
    xarRegisterPrivilege('EditCalendar','All','calendar','All','All','ACCESS_EDIT');
    xarRegisterPrivilege('AddCalendar','All','calendar','All','All','ACCESS_ADD');
    xarRegisterPrivilege('DeleteCalendar','All','calendar','All','All','ACCESS_DELETE');
    xarRegisterPrivilege('AdminCalendar','All','calendar','All','All','ACCESS_ADMIN');
    xarMakePrivilegeRoot('ViewCalendar');
    xarMakePrivilegeRoot('ReadCalendar');
    xarMakePrivilegeRoot('CommentCalendar');
    xarMakePrivilegeRoot('ModerateCalendar');
    xarMakePrivilegeRoot('EditCalendar');
    xarMakePrivilegeRoot('AddCalendar');
    xarMakePrivilegeRoot('DeleteCalendar');
    xarMakePrivilegeRoot('AdminCalendar');

# --------------------------------------------------------
#
# Set up modvars
#

    // Location of the PEAR Calendar Classes
    // Use the PHP Include path for now
    xarModSetVar('calendar','pearcalendar_root','modules/calendar/pear/Calendar/');

    // get list of calendar ics files
    $data = xarModAPIFunc('calendar', 'admin', 'get_calendars');
    xarModSetVar('calendar','default_cal',serialize($data['icsfiles']));

    // Other variables from phpIcalendar config.inc.php
//    xarModSetVar('calendar','default_view'           , 'week');
    xarModSetVar('calendar','minical_view'           , 'week');
//    xarModSetVar('calendar','cal_sdow'               , 0);   // 0=sunday $week_start_day in phpIcalendar
//    xarModSetVar('calendar','day_start'              , '0700');
//    xarModSetVar('calendar','day_end'                , '2300');
//    xarModSetVar('calendar','gridLength'             , 15);
    xarModSetVar('calendar','num_years'              , 1);
    xarModSetVar('calendar','month_event_lines'      , 1);
    xarModSetVar('calendar','tomorrows_events_lines' , 1);
    xarModSetVar('calendar','allday_week_lines'      , 1);
    xarModSetVar('calendar','week_events_lines'      , 1);
    xarModSetVar('calendar','second_offset'          , 0);
    xarModSetVar('calendar','bleed_time'             , 0);
    xarModSetVar('calendar','display_custom_goto'    , 0);
    xarModSetVar('calendar','display_ical_list'      , 1);
    xarModSetVar('calendar','allow_webcals'          , 0);
    xarModSetVar('calendar','this_months_events'     , 1);
    xarModSetVar('calendar','use_color_cals'         , 1);
    xarModSetVar('calendar','daysofweek_dayview'     , 0);
    xarModSetVar('calendar','enable_rss'             , 1);
    xarModSetVar('calendar','show_search'            , 1);
    xarModSetVar('calendar','allow_preferences'      , 1);
    xarModSetVar('calendar','printview_default'      , 0);
    xarModSetVar('calendar','show_todos'             , 1);
    xarModSetVar('calendar','show_completed'         , 0);
    xarModSetVar('calendar','allow_login'            , 0);

    // Regulate display in day view
    xarModVars::set('calendar','windowwidth', 902);
    xarModVars::set('calendar','minutesperunit', 15);
    xarModVars::set('calendar','unitheight', 12);

    xarModVars::set('calendar','default_view', 'week');
    xarModVars::set('calendar','cal_sdow', 0);
    xarModVars::set('calendar','day_start', 25200);
    xarModVars::set('calendar','day_end', 82800);

//TODO::Register the Module Variables
    //
    //xarModSetVar('calendar','allowUserCalendars',false);
    //xarModSetVar('calendar','eventsOpenNewWindow',false);
    //xarModSetVar('calendar','adminNotify',false);
    //xarModSetVar('calendar','adminEmail','none@none.org');

//TODO::Figure out all the permissions stuff
    // allow users to see the calendar w/ events
    xarRegisterMask('ViewCalendar','All','calendar','All','All','ACCESS_READ');
    // allow full admin of the calendar
    xarRegisterMask('AdminCalendar','All','calendar','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#  Register block types
#
    xarModAPIFunc('blocks', 'admin','register_block_type', array('modName' => 'calendar','blockType' => 'calnav'));
    xarModAPIFunc('blocks', 'admin','register_block_type', array('modName' => 'calendar','blockType' => 'month'));

//TODO::Register our blocklayout tags to allow using Objects in the templates
//<xar:calendar-decorator object="$Month" decorator="Xaraya" name="$MonthURI" />
//<xar:calendar-build object="$Month" />
//<xar:set name="$Month">& $Year->fetch()</xar:set>

    xarModSetVar('calendar', 'SupportShortURLs', true);

    xarTplRegisterTag(
        'calendar', 'calendar-decorator', array(),
        'calendar_userapi_handledecoratortag'
    );

# --------------------------------------------------------
#
# Create DD objects
#
    $module = 'calendar';
    $objects = array(
                   'calendar_calendar',
                   'calendar_event',
                     );

    if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

    return true;
}

/**
 *  Module Upgrade Function
 */
function calendar_upgrade($oldversion)
{

    switch ($oldversion) {
        case '0.1.0':
            // Start creating the tables

            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $calfilestable = $xartable['calendars_files'];
            xarDBLoadTableMaintenanceAPI();
            $fields = array(
                'xar_calendars_id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'primary_key' => true),
                'xar_files_id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'primary_key' => true)
                );
            $query = xarDBCreateTable($calfilestable, $fields);
            if (empty($query)) return;
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            $filestable = $xartable['calfiles'];
            xarDBLoadTableMaintenanceAPI();
            $fields = array(
                'xar_id' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
                'xar_path' => array('type' => 'varchar', 'size' => '255', 'null' => true)
                );
            $query = xarDBCreateTable($filestable, $fields);
            if (empty($query)) return;
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            $index = array(
                'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_calendars_files_calendars_id',
                'fields'    => array('xar_calendars_id'),
                'unique'    => false
            );
            $query = xarDBCreateIndex($calfilestable,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            $index = array(
                'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_calendars_files_files_id',
                'fields'    => array('xar_files_id'),
                'unique'    => false
            );
            $query = xarDBCreateIndex($calfilestable,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            return calendar_upgrade('0.1.1');
    }
    return true;
}

/**
 *  Module Delete Function
 */
function calendar_delete()
{

    xarTplUnregisterTag('calendar-decorator');
    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => 'calendar'));
/*
    // Remove all tables (see example module for comments)
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();

    $query = xarDBDropTable($xartable['calendars']);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBDropTable($xartable['calendars_files']);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBDropTable($xartable['calfiles']);
    if (empty($query)) return;
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // remove all module vars
    xarModDelAllVars('calendar');

    // Remove Masks and Instances
    xarRemoveMasks('calendar');
    xarRemoveInstances('calendar');

    // remove registered template tags
    xarTplUnregisterTag('calendar-decorator');

    return true;
    */
}

?>
