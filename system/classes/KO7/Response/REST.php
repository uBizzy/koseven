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

        // Check if body is array, else convert it to one by creating an array with "body" as index
        if ( ! is_array($content))
        {
            $content = [
                'body' => $content
            ];
        }

		$this->_body = $content;

		return $this;
	}

    /**
     * Set response as attachment (file download)
     *
     * @param string $attachment filename of attachment
     * @param string $type       file type/extension (e.g json, xml, etc..)
     *
     * @return $this
     */
	public function attachment(string $attachment, $type) : self
    {
        // Check if attachment is valid
        if (Valid::regex($attachment, '/^[-\pL\pN_, ]++$/uD'))
        {
            $this->headers('content-disposition', 'attachment; filename='.$attachment.'.'.$type);
        }

        return $this;
    }
}