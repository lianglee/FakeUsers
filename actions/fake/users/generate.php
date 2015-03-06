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
$users = new FakeUsers;

//get settings from admin
$count = input('count');
$password = input('password');

set_time_limit(0);
$users->AddFakeUsers($count, $password);

ossn_trigger_message(ossn_print('fakeusers:created', array($count)));
redirect(REF);