<?php
namespace T3o\T3oRedmine\Service;

/**
 * Class RestService
 *
 * Simple RestService to get data from a
 * Restful API using an apiKey and URL

 * Uses json format for all requests
 */
abstract class RestService {

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $apikey;

	/**
	 * @param array $config
	 */
	public function __construct($config) {
		$this->url = $config['url'];
		$this->apikey = $config['apikey'];
	}

	/**
	 * Runs the request and if successfull returns
	 * the response as a JSON serialized string.
	 * Return FALSE if something goes wrong
	 *
	 * @param string $url
	 * @param string $method GET|POST|PUT|DELETE
	 * @param string $data   Data to send with POST|PUT
	 * @param int $port
	 * @return object
	 */
	public function runRequest($url, $method = 'GET', $data = '', $port = 443) {
		$curl = curl_init();

		// Authentication
		if (isset($this->apikey)) {
			curl_setopt($curl, CURLOPT_USERPWD, $this->apikey);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}

		// Request
		$method = strtoupper($method);
		switch ($method) {
			case 'POST':
				curl_setopt($curl, CURLOPT_POST, 1);
				if (isset($data)) {
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				}
				break;
			case 'PUT':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
				if (isset($data)) {
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				}
				break;
			case 'DELETE':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
			default: // get
				break;
		}

		// Run the request
		try {
			curl_setopt($curl, CURLOPT_URL, $this->url . $url);
			curl_setopt($curl, CURLOPT_PORT, $port);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_VERBOSE, 0);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_TIMEOUT, 300);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, FALSE);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
					'Content-Type: text/json',
					'Content-length: ' . strlen($data),
					'Keep-Alive: 300'
				)
			);
			$response = curl_exec($curl);

			if (curl_errno($curl)) {
				curl_close($curl);
				return FALSE;
			}

			curl_close($curl);
		} catch (Exception $e) {
			return FALSE;
		}

		if ($response) {
			if (substr($response, 0, 1) == '{') {
				return json_decode($response, JSON_FORCE_OBJECT);
			} else {
				return FALSE;
			}
		}
		return FALSE;
	}
}
