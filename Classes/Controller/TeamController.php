<?php
require_once(PATH_typo3conf . '/ext/t3o_redmine/Classes/Service/RedmineService.php');

/**
 * Class Tx_T3oRedmine_Controller_TeamController
 */
class Tx_T3oRedmine_Controller_TeamController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var array
	 */
	protected $allowedRoles = array();

	/**
	 * @var Tx_T3oAjaxlogin_Domain_Repository_UserRepository
	 */
	protected $feUserRepository = NULL;

	/**
	 * Inject settings from typoscript and FlexForm to $this->settings
	 *
	 * @return void
	 */
	public function initializeAction() {
		$this->allowedRoles = $this->settings['roleIds'];
		$this->feUserRepository = t3lib_div::makeInstance('Tx_T3oAjaxlogin_Domain_Repository_UserRepository');
	}

	/**
	 * @return void
	 */
	public function indexAction() {
		$config['url'] = $this->settings['url'];
		$extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3o_redmine']);
		$config['apikey'] = $extConfig['apiKey'];

		/** @var RedmineService $restService */
		$restService = t3lib_div::makeInstance('RedmineService', $config);

		$members = $restService->getMembers($this->settings['project']);
		$sortedGroups = $this->filterMembers($members);
		$this->view->assign('groups', $sortedGroups);
	}

	/**
	 * Assign members to their specific roles.
	 *
	 * All the roles that are defined in typoscript
	 * will be shown, the other will be removed.
	 *
	 * Roles will be sorted in the order that they appear
	 * in typoscript (see Configuration/TypoScript/setup.txt)
	 *
	 * @param array $members
	 * @return array
	 */
	protected function filterMembers($members) {
		$filteredGroups = array();
		$sortedGroups = array();

		foreach ($members as $member) {
			$role = $this->getRoleOfMember($member);
			if ($role !== NULL) {
				$user = $this->feUserRepository->findOneByUsername($member['user']['login']);
				$filteredGroups[$role['id']][] = array(
					'id' => $member['user']['id'],
					'name' => $member['user']['name'],
					'login' => $member['user']['login'],
					'user' => $user,
					'role' => $role['name']
				);

			}
		}

		foreach ($this->allowedRoles as $role) {
			if (array_key_exists($role, $filteredGroups)) {
				$sortedGroups[] = $filteredGroups[$role];
			}
		}

		return $sortedGroups;
	}

	/**
	 * Return the most high-ranked role of the user or NULL
	 * if the users role is not defined in typoscript
	 *
	 * @param array $member
	 * @return array|NULL
	 */
	protected function getRoleOfMember($member) {
		foreach ($this->allowedRoles as $role) {
			foreach ($member['roles'] as $memberRole) {
				if ($memberRole['id'] == $role) {
					return $memberRole;
				}
			}
		}
		return NULL;
	}
}
