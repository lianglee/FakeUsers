<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    Open Social Website Core Team <info@informatikon.com>
 * @copyright 2014 iNFORMATIKON TECHNOLOGIES
 * @license   General Public Licence http://www.opensource-socialnetwork.org/licence
 * @link      http://www.opensource-socialnetwork.org/licence
 */

/* Define Paths */
define('__FAKE_USERS__', ossn_route()->com . 'FakeUsers/');

require_once(__FAKE_USERS__ . 'classes/FakeUsers.php');

function fake_users_init(){
	ossn_register_com_panel('FakeUsers', 'settings');
	
    if (ossn_isAdminLoggedin()) {
        ossn_register_action('fake/users/generate', __FAKE_USERS__ . 'actions/fake/users/generate.php');
        ossn_register_action('fake/users/delete', __FAKE_USERS__ . 'actions/fake/users/delete.php');
    }
}
ossn_register_callback('ossn', 'init', 'fake_users_init');
