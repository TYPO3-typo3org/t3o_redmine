<?php
namespace T3o\T3oRedmine\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;

/**
 * Class Tx_T3oRedmine_Controller_TeamController
 */
class TeamController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var array
	 */
	protected $allowedRoles = array();

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
	 */
	protected $feUserRepository = NULL;

    /**
     * injectMemberRepository
     *
     * @param \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository $feUserRepository
     * @return void
     */
    public function injectFeUserRepository(FrontendUserRepository $feUserRepository)
    {
        $this->feUserRepository = $feUserRepository;
    }

	/**
	 * Inject settings from typoscript and FlexForm to $this->settings
	 *
	 * @return void
	 */
	public function initializeAction() {
		$this->allowedRoles = $this->settings['roleIds'];
	}

	/**
	 * @return void
	 */
	public function indexAction() {
		$config['url'] = $this->settings['url'];
		$config['apikey'] = $this->settings['apikey'];

		/** @var \T3o\T3oRedmine\Service\RedmineService $restService */
		$restService = GeneralUtility::makeInstance('\T3o\T3oRedmine\Service\RedmineService', $config);

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
