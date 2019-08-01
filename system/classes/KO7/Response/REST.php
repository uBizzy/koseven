<?php
class KO7_Response_REST extends Response {

	/**
	 * We overwrite the factory method so it creates instances of
	 * this class instead of "Response"
	 *
	 * @param array $config
	 *
	 * @return Response_REST
	 */
	public static function factory(array $config = []) : Response_REST
	{
		return new Response_REST($config);
	}

	/**
	 * We overwrite the body method so we can pass arrays to as body
	 * (which is very common for REST-API's
	 *
	 * @param null $content
	 *
	 * @return $this|mixed|string
	 */
	public function body($content = NULL)
	{
		if ($content === NULL)
		{
			return $this->_body;
		}

		$this->_body = $content;

		return $this;
	}
}