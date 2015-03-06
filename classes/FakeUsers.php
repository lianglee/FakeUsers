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
class FakeUsers extends OssnUser {
		/**
		 * addImage
		 *
		 * Add image for user 
		 * @Note: Don't call this method directly
		 * 
		 * @param int $guid Valid user id
		 *
		 * @return bool;
		 */
		private function addImage($guid) {
				
				if(empty($guid)) {
						return false;
				}
				$profile = new OssnProfile;
				$file    = new OssnFile;
				
				$file->owner_guid = $guid;
				$file->type       = 'user';
				$file->subtype    = 'profile:photo';
				$file->setFile('userphoto');
				$file->setPath('profile/photo/');
				
				if($file->addFile()) {
						$resize = $file->getFiles();
						$profile->addPhotoWallPost($file->owner_guid, $resize->{0}->guid);
						
						if(isset($resize->{0}->value)) {
								$guid      = $guid;
								$datadir   = ossn_get_userdata("user/{$guid}/{$resize->{0}->value}");
								$file_name = str_replace('profile/photo/', '', $resize->{0}->value);
								$sizes     = ossn_user_image_sizes();
								foreach($sizes as $size => $params) {
										$params  = explode('x', $params);
										$width   = $params[1];
										$height  = $params[0];
										$resized = ossn_resize_image($datadir, $width, $height, true);
										file_put_contents(ossn_get_userdata("user/{$guid}/profile/photo/{$size}_{$file_name}"), $resized);
								}
								
						}
						return true;
				}
				return false;
		}
		/**
		 * AddFakeUsers
		 *
		 * Generate Fake Users
		 * 
		 * @param int $count Number of users you want to add
		 * @param string $password a default password for fake users
		 *
		 * @return bool;
		 */
		public function AddFakeUsers($count = 10, $password = '123456') {
				if(empty($count)){ 
					$count = 10;
				}
				if(empty($password)){
					$password = '123456';
				}
				require_once(__FAKE_USERS__ . 'classes/Faker/autoload.php');
				$faker = Faker\Factory::create();
				for($i = 0; $i < $count; $i++) {
						$this->username   = $faker->userName;
						$this->first_name = $faker->firstName;
						$this->last_name  = $faker->lastName;
						$this->email      = $faker->safeEmail;
						$this->password   = $password;
						$this->gender     = 'male';
						$this->birthdate  = date('d/m/Y');
						$this->usertype   = 'normal';
						
						if($this->addUser()) {
								
								$this->subtype = 'fake_users';
								$this->value   = $this->owner_guid;
								$this->add();
								
								$image               = $faker->imageURL(300, 300);
								$_FILES['userphoto'] = array(
										'name' => md5($image) . '.jpg',
										'tmp_name' => $image,
										'type' => 'image/jpeg',
										'size' => strlen(file_get_contents($image)),
										'error' => UPLOAD_ERR_OK
								);
								
								$this->addImage($this->owner_guid);
						}
				}
				return false;
		}
		/**
		 * getFakeUsers
		 *
		 * Get fake generated users
		 * @Note: Don't call this method directly
		 *
		 * @return false|array;
		 */
		public function getFakeUsers() {
				$params['from']   = 'ossn_entities as e';
				$params['params'] = array(
						'u.guid, u.username, u.first_name, u.last_name, u.email, u.last_login, u.last_activity, u.type'
				);
				$params['joins']  = "JOIN ossn_entities_metadata as emd ON e.guid=emd.guid JOIN ossn_users as u ON emd.value=u.guid";
				$params['wheres'] = array(
						"e.subtype='fake_users'"
				);
				$users            = $this->select($params, true);
				if($users) {
						foreach($users as $user) {
								$fakeuser    = get_object_vars($user);
								$fakeusers[] = arrayObject($fakeuser, 'OssnUser');
						}
						return $fakeusers;
				}
				return false;
		}
		/**
		 * deleteFakeUsers
		 *
		 * Delete fake users
		 *
		 * @return true;
		 */
		public function deleteFakeUsers() {
				$users = $this->getFakeUsers();
				if($users) {
						foreach($users as $user) {
								$user->deleteUser();
						}
						return true;
				}
		}
}