<?php
require_once(PATH_typo3conf . '/ext/t3o_redmine/Classes/Service/RestService.php');

/**
 * Class RedmineService
 *
 * Functions that directly request data using cURL
 */
class RedmineService extends RestService {

	/**
	 * Get the members that are part of a project
	 *
	 * @param string $projectName
	 * @return array
	 */
	public function getMembers($projectName) {
		$response = $this->runRequest('/projects/' . $projectName . '/memberships.json');
		return $response['memberships'];
	}
}
