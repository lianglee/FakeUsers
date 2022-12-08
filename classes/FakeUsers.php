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
		 * Get random image
		 *
		 * @return string
		 */
		private function getRandomImage() {
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
				curl_setopt($curl, CURLOPT_URL, 'https://picsum.photos/300/300');
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1');
				curl_setopt($curl, CURLOPT_CAINFO, ossn_route()->www . 'vendors/cacert.pem');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($curl);
				curl_close($curl);
				return $result;
		}
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
				$profile = new OssnProfile();
				$file    = new OssnFile();

				$file->owner_guid = $guid;
				$file->type       = 'user';
				$file->subtype    = 'profile:photo';
				$file->setFile('userphoto');
				$file->setPath('profile/photo/');
				$file->setExtension(array(
						'jpg',
						'png',
						'jpeg',
						'gif',
				));
				$u = ossn_user_by_guid($guid);

				if($fileguid = $file->addFile()) {
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
						//update user icon time, this time has nothing to do with photo entity time
						$u->data->icon_time = time();

						//Default profile picture #1647
						$u->data->icon_guid = $fileguid;
						$u->save();
						return true;
				}
				$u->deleteUser();
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
		public function AddFakeUsers($count = 10, $password = '123456789') {
				if(empty($count)) {
						$count = 10;
				}
				if($count > 0) {
						$count = $count - 1;
				}
				if(empty($password)) {
						$password = '123456789';
				}
				require_once __FAKE_USERS__ . 'classes/Faker/autoload.php';
				$faker = Faker\Factory::create();

				$i       = 0;
				$userdir = ossn_get_userdata('tmp/fakeusers/');
				if(!is_dir($userdir)) {
						mkdir($userdir, 0755, true);
				}
				do {
						$this->username   = $faker->userName;
						$this->first_name = $faker->firstName;
						$this->last_name  = $faker->lastName;
						$this->email      = $faker->safeEmail;
						$this->password   = $password;
						$this->gender     = 'male';
						$this->birthdate  = date('d/m/Y');
						$this->usertype   = 'normal';
						$this->validated  = true;

						if($guid = $this->addUser()) {
								$i++;
								$this->subtype = 'fake_users';
								$this->value   = $this->owner_guid;
								$this->add();

								$filename         = md5($guid) . '.jpg';
								$downloaded_image = $this->getRandomImage();
								$tempname         = $userdir . $filename;
								file_put_contents($tempname, $downloaded_image);
								$_FILES['userphoto'] = array(
										'name'     => $filename,
										'tmp_name' => $tempname,
										'type'     => 'image/jpeg',
										'size'     => strlen($downloaded_image),
										'error'    => UPLOAD_ERR_OK,
								);
								$this->addImage($this->owner_guid);
								unlink($tempname);
						}
				} while($i <= $count);
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
		public function getFakeUsers($count = false) {
				$options = array(
						'entities_pairs' => array(
								array(
										'name'   => 'fake_users',
										'value'  => false,
										'wheres' => 'emd0.value <> ""',
								),
						),
				);
				if($count) {
						$options['count'] = true;
				}
				return $this->searchUsers($options);
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