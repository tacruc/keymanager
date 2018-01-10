<?php
namespace OCA\KeyManager\Controller;

use OCP\IRequest;
use OCP\IGpg;
use OCP\ILogger;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\DataDownloadResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCP\IUser;
use OCP\IUserManager;

class KeyController extends Controller {
	private $userId;

	private $gpg;

	private $userManager;

	private $logger;
	public function __construct($AppName, IRequest $request, ILogger $logger, IUserManager $userManager, IGpg $gpg, $UserId){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->gpg = $gpg;
		$this->logger = $logger;
		$this->userManager = $userManager;
	}




	/**
	 * @NoAdminRequired
	 */
	public function index() {
		$index = $this->gpg->keyinfo('', $uid = $this->userId);
		$keys = $this->userManager->get($this->userId)->getPublicKeys();
		$contactsManager = \OC::$server->getContactsManager();
		$contacts = $contactsManager->search("",['X-KEY-FINGERPRINT']);
		foreach ($contacts as $contact){
			if(isset($contact['X-KEY-FINGERPRINT'])) {
				$keys = array_merge($keys, $contact['X-KEY-FINGERPRINT']);
			}
		}
		foreach ($keys as $key) {
			$index = array_merge($index, $this->gpg->keyinfo($key));
		}
		return new JSONResponse($this->convertKeyinfo($index));
	}

	/**
	 * @NoAdminRequired
	 * @param int $id
	 */
	public function show($id) {
		$result = $this->convertKeyinfo($this->gpg->keyinfo($id,$uid = $this->userId));
		return new JSONResponse($result);
	}

	/**
	 * @NoAdminRequired
	 * @param string $data
	 */
	public function create($data) {
		$fingerprint = $this->gpg->import($data, $uid = $this->userId)['fingerprint'];
		return new JSONResponse($this->show($fingerprint));
	}

	/**
	 * @NoAdminRequired
	 * @param string $id
	 */
	public function delete($id) {
		$this->gpg->deletekey($id, $uid = $this->userId);
		return new DataResponse();
	}

	/**
	 * @NoAdminRequired
	 * @param string $id
	 */
	public function revoke($id, $revocationCertifikate) {
		return new DataResponse();
	}

	/**
	 * @NoAdminRequired
	 * @param string $id
	 */
	public function generateRevokeCertificate($id) {
		return new DataResponse();
	}

	/**
	 * @NoAdminRequired
	 * @param string $id
	 */
	public function setDefault($id) {
		\OC::$server->getUserManager()->get($this->userId)->setDefaultPublicKey($id);
		return new DataResponse();
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 * @param string $filename
	 */
	public function publicServerKey($filename) {
		if ($filename === "server" OR $filename === "public") {
			$fingerprint = \OC::$server->getConfig()->getSystemValue('GpgServerKey', '');
			$keydata = $this->gpg->export($fingerprint);
		} else {
			$keydata = "";
		}
		return new DataDownloadResponse($keydata, $filename.".asc", "text/plain");
	}

	/**
	 * Converts gpg Keyinfo to info array send as json to javascript
	 * @param array $array
	 * @return array
	 */
	private function convertKeyinfo($array){
		$contactsManager = \OC::$server->getContactsManager();
		$final = array();
		foreach ($array as $key) {
			$fingerprint = $key['subkeys'][0]['fingerprint'];
			$expires = $key['subkeys'][0]['expires'];
			$valid = $key['subkeys'][0]['timestamp'];
			if (array_key_exists($fingerprint,$final)) {
				continue;
			}
			$entry = ['private' => $key["is_secret"],
					'identities' => [],
					'expires' => $expires,
					'valid' => $valid,
					'detail' => '', //Fixme: on version 1.5 of gnupg it should be possible to read the algorithm and length https://github.com/php-gnupg/php-gnupg/issues/6
					'fingerprint' => $fingerprint,
					];
			if ($contactsManager->isEnabled()) {
				$contacts = $contactsManager->search($fingerprint,['X-KEY-FINGERPRINT']);
			} else {
				$contacts = array();
			}
			if (sizeof($contacts) > 0) {
				$entry['identities'][0] = FALSE;
			}
			$used_contacts = array();
			foreach ($key['uids'] as $uid) {
				unset($uid['uid']);
				foreach ($contacts as $contact){
					if(isset($contact['EMAIL']) && in_array($uid['email'],$contact['EMAIL'])){
						$uid['contacts'][] = $contact;
						$used_contacts[] = $contact['UID'];
					}
				}
				if (isset($uid['contacts']) && isset($entry['identities'][0]) && sizeof($uid['contacts']) > 0 && $entry['identities'][0] === FALSE) {
					$entry['identities'][0] = $uid;
				} else {
					$entry['identities'][] = $uid;
				}
			}
			foreach ($contacts as $contact){
				if (!in_array($contact['UID'],$used_contacts)){
					$uid = [
						'name' => $contact['FN'],
						'email' => '',
						'comment' => ''
					];
					$uid['contacts'][] = $contact;
					if ($entry['identities'][0] === FALSE)  {
						$entry['identities'][0] = $uid;
					} else {
						$entry['identities'][] = $uid;
					}
				}
			}
			$final[$fingerprint] = $entry;
		}
		return $final;
	}
}
