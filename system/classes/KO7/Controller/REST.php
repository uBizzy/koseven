<?php
/**
 * Abstract Controller class for REST controller mapping.
 * Supports GET, PUT, POST, and DELETE.
 *
 * @package        KO7\REST
 *
 * @copyright  (c) since 2016 Koseven Team
 * @license        https://koseven.ga/LICENSE
 */
abstract class KO7_Controller_REST extends Controller {

    /**
     * Default Output (only if allowed by configuration)
     *
     * @var string
     */
    public static $default_output = 'json';

    /**
     * The output format to be used (JSON, XML etc.).
     *
     * @var string
     */
    public $output_format;

    /**
     * The response that will be returned from controller
     *
     * @var  Response_REST
     */
    public $response;

    /**
     * REST types
     *
     * @var array
     */
    protected static $_action_map = [
        HTTP_Request::GET => 'index',
        HTTP_Request::PUT => 'update',
        HTTP_Request::POST => 'create',
        HTTP_Request::DELETE => 'delete'
    ];

    /**
     * Instance of body formatting class
     *
     * @var REST_Format
     */
    protected $_formatter;

    /**
     * Allows overwriting the content_type in the response header
     * If this is null, content type will be evaluated from the output_format
     *
     * @var string
     */
    protected $_content_type;

    /**
     * The request's parameters.
     *
     * @var array
     */
    protected $_params = [];

    /**
     * KO7_Controller_REST constructor.
     *
     * We need to convert the "Response" object to a "Response_REST" object.
     * This allows as passing arrays and objects as content body and not only strings
     *
     * @param Request  $request  Request that created the controller
     * @param Response $response The request's response
     */
    public function __construct(Request $request, Response $response)
    {
        // Copy class properties
        $config = [
            '_status' => $response->status(),
            '_body' => $response->body(),
            '_cookies' => $response->cookie(),
            '_protocol' => $response->protocol()
        ];

        // Copy all http header(s)
        $config += $response->headers()->getArrayCopy();

        // Instance Response_REST and pass it to the constructor
        parent::__construct($request, Response_REST::factory($config));
    }

    /**
     * Automatically executed before the controller action.
     * Evaluate Request (method, action, parameter, format)
     *
     * @throws HTTP_Exception
     */
    public function before() : void
    {
        // Parent call
        parent::before();

        // Try fetching method from HTTP_X_HTTP_METHOD_OVERRIDE, otherwise use the one sent with the request
        $ovr = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? $this->request->method();
        $this->request->method(array_key_exists($ovr, static::$_action_map) ? $ovr : $this->request->method());

        // Determine the request action from the request method, if the action/method is not allowed throw error
        if ( ! isset(static::$_action_map[$this->request->method()]))
        {
            $this->response->status(405)->headers('Allow', implode(', ', array_keys(static::$_action_map)));
        }
        else
        {
            $this->request->action(static::$_action_map[$this->request->method()]);
        }

        // Initialize request parameter
        $this->_params = $this->_parse_params();

        // Get output format from route - use default if not available
        $this->output_format = $this->request->param('format') ?: static::$default_output;

        // Try initializing the formatter
        try
        {
            $this->_formatter = REST_Format::factory(strtoupper($this->output_format), $this->request);
        }
        catch (REST_Exception $e)
        {
            throw HTTP_Exception::factory(500, $e->getMessage(), NULL, $e);
        }
    }

    /**
     * Automatically executed after the controller action.
     *
     * - Adds cache and content header(s).
     * - Formats body with given formatting method
     * - Adds attachment header if necessary
     */
    public function after() : void
    {
        // Parent call
        parent::after();

        // No cache / must-revalidate cache if method is not GET
        if ($this->request->method() !== HTTP_Request::GET)
        {
            $this->response->headers('cache-control', 'no-cache, no-store, max-age=0, must-revalidate');
        }

        // Format body
        $body = $this->_formatter->format($this->response->body());

        // Parse and set response headers
        $this->response->headers('content-type', $this->_content_type ?? File::mime_by_ext($this->output_format));

        // Support attachment header
        if (isset($this->_params['attachment']))
        {
            $this->response->attachment($this->_params['attachment'], $this->output_format);
        }

        // Set response body
        $this->response->body($body);
    }

    /**
     * Initializes the request params array based on the current request.
     *
     * @return array
     */
    protected function _parse_params() : array
    {
        // If method is GET, fetch params from query
        if ($this->request->method() === HTTP_Request::GET)
        {
            return array_merge($this->request->query(), $this->_params);
        }

        // Otherwise we have a PUT, POST, DELETE Method
        // If content_type is JSON we need to decode the body first
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE)
        {
            $parsed_body = json_decode($this->request->body(), TRUE);
        }
        else
        {
            parse_str($this->request->body(), $parsed_body);
        }

        return array_merge((array)$parsed_body, (array)$this->request->post());
    }

}