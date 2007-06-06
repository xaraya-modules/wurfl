<?php
/**
 *
 * Function purpose to be added
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Foo Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  to be added
 *
 * @param to be added
 * @return to be added
 *
 */

function foo_user_usermenu($args)
{

    // Security check
    if (!xarSecurityCheck('ViewRoles')) return;
    extract($args);
    if(!xarVarFetch('phase','notempty', $phase, 'menu', XARVAR_NOT_REQUIRED)) {return;}
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Your Account Preferences')));
    $data = array(); $hooks = array();
    switch(strtolower($phase)) {
        case 'menu':
            $iconbasic = 'modules/roles/xarimages/home.gif';
            $iconenhanced = 'modules/roles/xarimages/home.gif';
            $current = xarModURL('roles', 'user', 'account', array('moduleload' => 'foo'));
            $data = xarTplModule('foo','user', 'user_menu_icon', array('iconbasic'    => $iconbasic,
                                                                         'iconenhanced' => $iconenhanced,
                                                                         'current'      => $current));
            break;
        case 'form':

            $stub = basename(xarServerGetCurrentURL());

            switch(strtolower($stub)) {
                case 'tab1':
                    // get some roles properties, might be useful
                    $uname = xarUserGetVar('uname');
                    $name = xarUserGetVar('name');
                    $id = xarUserGetVar('id');
                    $email = xarUserGetVar('email');
                    $role = xarUFindRole($uname);
                    $home = $role->getHome();
                    $authid = xarSecGenAuthKey();
                    $submitlabel = xarML('Submit');
                    $item['module'] = 'roles';

                    $hooks = xarModCallHooks('item','modify',$id,$item);
                    if (isset($hooks['dynamicdata'])) {
                        unset($hooks['dynamicdata']);
                    }

                    $data = xarTplModule('foo','user', 'user_menu_tab1',
                                          array('authid'       => $authid,
                                          'withupload'   => $withupload,
                                          'name'         => $name,
                                          'uname'        => $uname,
                                          'home'         => $home,
                                          'hooks'        => $hooks,
                                          'emailaddress' => $email,
                                          'submitlabel'  => $submitlabel,
                                          'id'          => $id));
                    break;

                case 'tab2':
                    $data = xarTplModule('foo','user', 'user_menu_tab2');
                    break;
            }
            break;

        case 'updatebasic':

            // Confirm authorisation code.
//            if (!xarSecConfirmAuthKey()) return;

            xarModCallHooks('item', 'update', $id, $item);

            // Redirect
            xarResponseRedirect(xarModURL('roles', 'user', 'account'));
            return true;
    }
    return $data;
}
?>
