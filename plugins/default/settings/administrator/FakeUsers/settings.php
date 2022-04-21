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
 
echo ossn_view_form('add', array(
    'action' => ossn_site_url() . 'action/fake/users/generate',
    'component' => 'FakeUsers',
    'class' => 'ossn-admin-form'	
), false);

$users = new FakeUsers;
$list = $users->getFakeUsers();
$count = $users->getFakeUsers(true);
?>
<div class="top-controls top-controls-users-page">
    <a href="<?php echo ossn_site_url("action/fake/users/delete", true); ?>"
       class="ossn-admin-button button-red">Delete All Fake Users</a>
</div>
<table class="table ossn-users-list">
    <tbody>
    <tr class="table-titles">
        <td><?php echo ossn_print('name'); ?></td>
        <td><?php echo ossn_print('username'); ?></td>
        <td><?php echo ossn_print('email'); ?></td>
        <td><?php echo ossn_print('type'); ?></td>
        <td><?php echo ossn_print('lastlogin'); ?></td>
        <td><?php echo ossn_print('edit'); ?></td>
        <td><?php echo ossn_print('delete'); ?></td>
    </tr>
    <?php foreach ($list as $user) {
        $user = ossn_user_by_guid($user->guid);
		$lastlogin = '';
		if(!empty($user->last_login)){
			$lastlogin = ossn_user_friendly_time($user->last_login);
		}
        ?>
        <tr>
            <td>
                <div class="image"><img
                        src="<?php echo ossn_site_url(); ?>avatar/<?php echo $user->username; ?>/smaller"/></div>
                <div class="name"
                     style="margin-left:39px;margin-top: -39px;min-height: 30px;"><?php echo strl($user->fullname, 20); ?></div>
            </td>
            <td><?php echo $user->username; ?></td>
            <td><?php echo $user->email; ?></td>
            <td><?php echo $user->type; ?></td>
            <td><?php echo $lastlogin; ?></td>
            <td>
                <a href="<?php echo ossn_site_url("administrator/edituser/{$user->username}"); ?>"><?php echo ossn_print('edit'); ?></a>
            </td>
            <td><a href="<?php echo ossn_site_url("action/admin/delete/user?guid={$user->guid}", true); ?>" class="userdelete"><?php echo ossn_print('delete'); ?></a></td>

        </tr>
    <?php } ?>
    </tbody>
</table>
<?php echo ossn_view_pagination($count); ?>