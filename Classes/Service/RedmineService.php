<?php
namespace T3o\T3oRedmine\Service;

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
		$response = $this->runRequest('/projects/' . $projectName . '/memberships.json?limit=100');
		return $response['memberships'];
	}
}
