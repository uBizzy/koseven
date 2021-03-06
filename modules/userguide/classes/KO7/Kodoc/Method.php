<?php
/**
 * Class method documentation generator.
 *
 * @package    KO7/Userguide
 * @category   Base
 * 
 * @copyright  (c) 2007-2016  Kohana Team
 * @copyright  (c) since 2016 Koseven Team
 * @license    https://koseven.dev/LICENSE
 */
class KO7_Kodoc_Method extends Kodoc {

	/**
	 * @var  ReflectionMethod  The ReflectionMethod for this class
	 */
	public $method;

	/**
	 * @var  array  Array of Kodoc_Method_Param
	 */
	public $params;

	/**
	 * @var  array  The things this function can return
	 */
	public $return = [];

	/**
	 * @var  string  The source code for this function
	 */
	public $source;

	public function __construct($class, $method)
	{
		$this->method = new ReflectionMethod($class, $method);

		$this->class = $parent = $this->method->getDeclaringClass();

		if ($modifiers = $this->method->getModifiers())
		{
			$this->modifiers = '<small>'.implode(' ', Reflection::getModifierNames($modifiers)).'</small> ';
		}

		do
		{
			if ($parent->hasMethod($method) AND $comment = $parent->getMethod($method)->getDocComment())
			{
				// Found a description for this method
				break;
			}
		}
		while ($parent = $parent->getParentClass());

		list($this->description, $tags) = Kodoc::parse($comment);

		if (($file = $this->class->getFileName()) AND empty($this->class->getTraitNames()))
		{
			$this->source = Kodoc::source($file, $this->method->getStartLine(), $this->method->getEndLine());
		}

		if (isset($tags['param']))
		{
			$params = [];

			foreach ($this->method->getParameters() as $i => $param)
			{
				$param = new Kodoc_Method_Param([$this->method->class, $this->method->name],$i);

				if (isset($tags['param'][$i]))
				{
					preg_match('/^(\S+)(?:\s*(?:\$'.$param->name.'\s*)?(.+))?$/s', $tags['param'][$i], $matches);

					$param->type = $matches[1];

					if (isset($matches[2]))
					{
						$param->description = ucfirst($matches[2]);
					}
				}
				$params[] = $param;
			}

			$this->params = $params;

			unset($tags['param']);
		}

		if (isset($tags['return']))
		{
			foreach ($tags['return'] as $return)
			{
				if (preg_match('/^(\S*)(?:\s*(.+?))?$/', $return, $matches))
				{
					$this->return[] = [$matches[1], isset($matches[2]) ? $matches[2] : ''];
				}
			}

			unset($tags['return']);
		}

		$this->tags = $tags;
	}

	public function params_short()
	{
		$out = '';
		$required = TRUE;
		$first = TRUE;
		foreach ($this->params as $param)
		{
			if ($required AND $param->default AND $first)
			{
				$out .= '[ '.$param;
				$required = FALSE;
				$first = FALSE;
			}
			elseif ($required AND $param->default)
			{
				$out .= '[, '.$param;
				$required = FALSE;
			}
			elseif ($first)
			{
				$out .= $param;
				$first = FALSE;
			}
			else
			{
				$out .= ', '.$param;
			}
		}

		if ( ! $required)
		{
			$out .= '] ';
		}

		return $out;
	}

} // End Kodoc_Method
