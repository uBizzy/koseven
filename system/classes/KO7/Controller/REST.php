<?php
/**
 * Abstract Controller class for REST controller mapping.
 * Supports GET, PUT, POST, and DELETE.
 *
 * @package        REST\Controller
 *
 * @copyright  (c) 2007-2016  Kohana Team, Alon Pe'er, Adi Oz
 * @copyright  (c) since 2016 Koseven Team
 * @license        https://koseven.ga/LICENSE.md
 */
abstract class KO7_Controller_REST extends Controller {

	/**
	 * Default Output (only if allowed by configuration)
	 * @var string
	 */
	public static $default_output = 'json';

	/**
	 * REST types
	 * @var array
	 */
	protected static $_action_map = [
		HTTP_Request::GET => 'index',
		HTTP_Request::PUT => 'update',
		HTTP_Request::POST => 'create',
		HTTP_Request::DELETE => 'delete'
	];

	/**
	 * The output format to be used (JSON, XML etc.).
	 * @var string
	 */
	public $output_format;

	/**
	 * The request's parameters.
	 * @var array
	 */
	protected $_params;

	/**
	 * Method to use for formatting the body.
	 * @var string
	 */
	protected $_format_method;

	/**
	 * KO7_Controller_REST constructor.
	 *
	 * We need to convert the "Response" object to a "Response_REST" object.
	 *
	 * @param Request  $request   Request that created the controller
	 * @param Response $response  The request's response
	 */
	public function	__construct(Request $request, Response $response)
	{
		// Set status, body, cookies and protocol
		$config = [
			'_status'   => $response->status(),
			'_body'     => $response->body(),
			'_cookies'  => $response->cookie(),
			'_protocol' => $response->protocol()
		];

		// Fetch http header and put them inside our configuration
		foreach ($response->headers()->getArrayCopy() as $header => $value)
		{
			$config[$header] = $value;
		}

		// Init rest response and pass to parent constructor
		parent::__construct($request, Response_REST::factory($config));
	}

	/**
	 * Checks the requested method against the available methods. If the method
	 * is supported, sets the request action from the map. If not supported,
	 * and an alternative action wasn't set, the "invalid" action will be called.
	 *
	 * @throws HTTP_Exception
	 */
	public function before() : void
	{
		// Call parent constructor
		parent::before();

		// Allow setting method as parameter if request is GET
		$this->_overwrite_method();

		// Get request method
		$method = $this->request->method();

		// Set request action
		if ($this->request->action())
		{
			if ( ! isset(static::$_action_map[$method]))
			{
				$this->request->action('invalid');
			}
			else
			{
				$this->request->action(static::$_action_map[$method]);
			}
		}

		// Initialize request parameter / body
		$this->_init_params();

		// Get output format from route file extension.
		$this->output_format = $this->request->param('format') ?: static::$default_output;

		// Check if format method exists
		$format_method = '_format_' . $this->output_format;

		// Report an error if not
		if ( ! is_callable([$this, $format_method]))
		{
			// Status 500 = Internal Server error
			$this->response->status(500);

			throw new HTTP_Exception('Unknown format ":format" for REST-API Controller.', [
				':format'	=> $this->output_format
			]);
		}

		$this->_format_method = $format_method;
	}

	/**
	 * Adds a cache control header.
	 */
	public function after() : void
	{
		// Suppress response codes
		if (isset($this->_params['suppressResponseCodes']) && $this->_params['suppressResponseCodes'] === 'true')
		{
			$this->response->status(200);
		}

		// Set cache-control header if required
		if (in_array($this->request->method(), [HTTP_Request::PUT, HTTP_Request::POST, HTTP_Request::DELETE], TRUE))
		{
			$this->response->headers('cache-control', 'no-cache, no-store, max-age=0, must-revalidate');
		}

		// Check if body is array, else convert it to one
		$body = $this->response->body();
		if ( ! is_array($body))
		{
			$body = [
				'body' => $body
			];
		}

		// Parse and set response headers
		$body = $this->{$this->_format_method}($body);
		$this->response->headers('content-type', File::mime_by_ext($this->output_format));
		$this->response->headers('content-length', (string) strlen($body));

		// Support attachment header
		if (isset($this->_params['attachment']) && Valid::regex($this->_params['attachment'], '/^[-\pL\pN_, ]++$/uD'))
		{
			$this->response->headers('content-disposition', 'attachment; filename='. $this->_params['attachment'] .'.'. $this->output_format);
		}

		// Set response body
		$this->response->body($body);

		// Parent call
		parent::after();
	}

	protected function _format_json(array $data = [])
	{
		return json_encode($data);
	}

	/**
	 * Initializes the request params array based on the current request.
	 */
	protected function _init_params() : void
	{
		$this->_params = [];
		$method = $this->request->method();

		if (in_array($method, [HTTP_Request::POST, HTTP_Request::PUT, HTTP_Request::DELETE], TRUE))
		{
			if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE)
			{
				$parsed_body = json_decode($this->request->body(), TRUE);
			}
			else
			{
				parse_str($this->request->body(), $parsed_body);
			}
			$this->_params = array_merge((array)$parsed_body, (array)$this->request->post());
		}
		elseif ($method === HTTP_Request::GET)
		{
			$this->_params = array_merge((array)$this->request->query(), $this->_params);
		}
	}

	/**
	 * Implements support for setting the request method via a GET parameter.
	 */
	protected function _overwrite_method() : void
	{
		// Check if request is get and method is given as query parameter
		if (
			$this->request->method() === HTTP_Request::GET &&
			($method = $this->request->query('method')) &&
			in_array(strtoupper($method), [HTTP_Request::POST, HTTP_Request::PUT, HTTP_Request::DELETE], TRUE)
		)
		{
			$this->request->method($method);
		}
		else
		{
			// Try fetching method from HTTP_X_HTTP_METHOD_OVERRIDE before falling back on the detected method.
			$this->request->method(Arr::get($_SERVER, 'HTTP_X_HTTP_METHOD_OVERRIDE', $this->request->method()));
		}
	}

	/**
	 * Sends a 405 "Method Not Allowed" response and a list of allowed actions.
	 */
	public function action_invalid() : void
	{
		$this->response
			->status(405)
			->headers('Allow', implode(', ', array_keys(static::$_action_map)));
	}
}