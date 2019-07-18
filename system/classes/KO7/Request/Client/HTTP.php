<?php
/**
 * HTTP driver performs external requests using the
 * pecl_http php extension.
 *
 * NOTE: This driver is not used by default. To use it as default call:
 *
 *  Request_Client_External::$client = 'Request_Client_HTTP';
 *
 * before executing the request (ideally in your application bootstrap)
 *
 * @package        KO7\Request
 *
 * @copyright  (c) 2007-2016  Kohana Team
 * @copyright  (c) since 2016 Koseven Team
 * @license        https://koseven.ga/LICENSE
 *
 */
class KO7_Request_Client_HTTP extends Request_Client_External {

	/**
	 * Curl options
	 *
	 * @var  array
	 * @link http://www.php.net/manual/function.curl-setopt
	 */
	protected $_options = [];

	/**
	 * Sends the HTTP message [Request] to a remote server and processes
	 * the response.
	 *
	 * @param Request  $request  request to send
	 * @param Response $response response to send
	 *
	 * @throws KO7_Exception
	 *
	 * @return Response
	 */
	public function _send_message(Request $request, Response $response)
	{
		// Instance a new Client
		$client = new http\Client;

		// Set cookies
		$client->setCookies($request->cookie());

		// Instance HTTP Request Object
		$http_request = new \http\Client\Request($request->method(), $request->uri());

		// Set custom cURL options
		if ($this->_options)
		{
			$http_request->setOptions($this->_options);
		}

		// Set headers
		$http_request->setHeaders($request->headers()->getArrayCopy());

		// Set query (?foo=bar&bar=foo)
		$http_request->setQuery($request->query());

		// Set the body
		$http_request->getBody()->append($request->body());

		// Execute call
		$client->enqueue($http_request)->send();

		// Parse Response
		$http_response = $client->getResponse();

		// Build the response
		$response->status($http_response->getResponseCode())->headers($http_response->getHeaders())->cookie($http_response->getCookies())->body($http_response->getBody());

		return $response;
	}

}
