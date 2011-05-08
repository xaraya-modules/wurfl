<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * Manage definition of instances for privileges (unfinished)
 *
 * @return array for template
 */
function publications_admin_privileges($args)
{
    extract($args);

    // fixed params
    if (!xarVarFetch('ptid',         'isset', $ptid,         NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('cid',          'isset', $cid,          NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('uid',          'isset', $uid,          NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('author',       'isset', $author,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('id',           'isset', $id,           NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('apply',        'isset', $apply,        NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extpid',       'isset', $extpid,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extname',      'isset', $extname,      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extrealm',     'isset', $extrealm,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extmodule',    'isset', $extmodule,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extcomponent', 'isset', $extcomponent, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extinstance',  'isset', $extinstance,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extlevel',     'isset', $extlevel,     NULL, XARVAR_DONT_SET)) {return;}

    sys::import('modules.dynamicdata.class.properties.master');
    $categories = DataPropertyMaster::getProperty(array('name' => 'categories'));
    $cids = $categories->returnInput('privcategories');

    if (!empty($extinstance)) {
        $parts = explode(':',$extinstance);
        if (count($parts) > 0 && !empty($parts[0])) $ptid = $parts[0];
        if (count($parts) > 1 && !empty($parts[1])) $cid = $parts[1];
        if (count($parts) > 2 && !empty($parts[2])) $uid = $parts[2];
        if (count($parts) > 3 && !empty($parts[3])) $id = $parts[3];
    }

    if (empty($ptid) || $ptid == 'All' || !is_numeric($ptid)) {
        $ptid = 0;
        if (!xarSecurityCheck('AdminPublications')) return;
    } else {
        if (!xarSecurityCheck('AdminPublications',1,'Publication',"$ptid:All:All:All")) return;
    }

// TODO: do something with cid for security check

    // TODO: figure out how to handle more than 1 category in instances
    if (empty($cid) || $cid == 'All' || !is_numeric($cid)) {
        $cid = 0;
    }
    if (empty($cid) && isset($cids) && is_array($cids)) {
        foreach ($cids as $catid) {
            if (!empty($catid)) {
                $cid = $catid;
                // bail out for now
                break;
            }
        }
    }

    if (empty($id) || $id == 'All' || !is_numeric($id)) {
        $id = 0;
    }
    $title = '';
    if (!empty($id)) {
        $article = xarModAPIFunc('publications','user','get',
                                 array('id'      => $id,
                                       'withcids' => true));
        if (empty($article)) {
            $id = 0;
        } else {
            // override whatever other params we might have here
            $ptid = $article['pubtype_id'];
        // TODO: review when we can handle multiple categories and/or subtrees in privilege instances
            if (!empty($article['cids']) && count($article['cids']) == 1) {
                // if we don't have a category, or if we have one but this article doesn't belong to it
                if (empty($cid) || !in_array($cid, $article['cids'])) {
                    // we'll take that category
                    $cid = $article['cids'][0];
                }
            } else {
                // we'll take no categories
                $cid = 0;
            }
            $uid = $article['owner'];
            $title = $article['title'];
        }
    }

// TODO: figure out how to handle groups of users and/or the current user (later)
    if (strtolower($uid) == 'myself') {
        $uid = 'Myself';
        $author = 'Myself';
    } elseif (empty($uid) || $uid == 'All' || (!is_numeric($uid) && (strtolower($uid) != 'myself'))) {
        $uid = 0;
        if (!empty($author)) {
            $user = xarModAPIFunc('roles', 'user', 'get',
                                  array('name' => $author));
            if (!empty($user) && !empty($user['uid'])) {
                if (strtolower($author) == 'myself') $uid = 'Myself';
                else $uid = $user['uid'];
            } else {
                $author = '';
            }
        }
    } else {
        $author = '';
/*
        $user = xarModAPIFunc('roles', 'user', 'get',
                              array('uid' => $uid));
        if (!empty($user) && !empty($user['name'])) {
            $author = $user['name'];
        }
*/
    }

    // define the new instance
    $newinstance = array();
    $newinstance[] = empty($ptid) ? 'All' : $ptid;
    $newinstance[] = empty($cid) ? 'All' : $cid;
    $newinstance[] = empty($uid) ? 'All' : $uid;
    $newinstance[] = empty($id) ? 'All' : $id;

    if (!empty($apply)) {
        // create/update the privilege
        $id = xarReturnPrivilege($extpid,$extname,$extrealm,$extmodule,$extcomponent,$newinstance,$extlevel);
        if (empty($id)) return; // throw back

        // redirect to the privilege
        xarController::redirect(xarModURL('privileges', 'admin', 'modifyprivilege',
                                      array('id' => $id)));
        return true;
    }

    // get the list of current authors
    $authorlist =  xarModAPIFunc('publications','user','getauthors',
                                 array('ptid' => $ptid,
                                       'cids' => empty($cid) ? array() : array($cid)));
    if (!empty($author) && isset($authorlist[$uid])) {
        $author = '';
    }

    if (empty($id)) {
        $numitems = xarModAPIFunc('publications','user','countitems',
                                  array('ptid' => $ptid,
                                        'cids' => empty($cid) ? array() : array($cid),
                                        'owner' => $uid));
    } else {
        $numitems = 1;
    }
    $data = array(
                  'ptid'         => $ptid,
                  'cid'          => $cid,
                  'uid'          => $uid,
                  'author'       => xarVarPrepForDisplay($author),
                  'authorlist'   => $authorlist,
                  'id'          => $id,
                  'title'        => xarVarPrepForDisplay($title),
                  'numitems'     => $numitems,
                  'extpid'       => $extpid,
                  'extname'      => $extname,
                  'extrealm'     => $extrealm,
                  'extmodule'    => $extmodule,
                  'extcomponent' => $extcomponent,
                  'extlevel'     => $extlevel,
                  'extinstance'  => xarVarPrepForDisplay(join(':',$newinstance)),
                 );

    // Get publication types
    $data['pubtypes'] = xarModAPIFunc('publications','user','get_pubtypes');

    $catlist = array();
    if (!empty($ptid)) {
        $basecats = xarModAPIFunc('categories','user','getallcatbases',array('module' => 'publications', 'itemtype' => $ptid));
        foreach ($basecats as $catid) $catlist[$catid['id']] = 1;
        if (empty($data['pubtypes'][$ptid]['config']['owner']['label'])) {
            $data['showauthor'] = 0;
        } else {
            $data['showauthor'] = 1;
        }
    } else {
        foreach (array_keys($data['pubtypes']) as $pubid) {
            $basecats = xarModAPIFunc('categories','user','getallcatbases',array('module' => 'publications', 'itemtype' => $pubid));
            foreach ($basecats as $catid) {
                $catlist[$catid['id']] = 1;
            }
        }
        $data['showauthor'] = 1;
    }

    $seencid = array();
    if (!empty($cid)) {
        $seencid[$cid] = 1;
    }

    $data['cids'] = $cids;
    $data['cats'] = $catlist;
    $data['refreshlabel'] = xarML('Refresh');
    $data['applylabel'] = xarML('Finish and Apply to Privilege');
    return $data;
}

?>
