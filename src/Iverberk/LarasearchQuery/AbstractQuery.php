<?php namespace Iverberk\LarasearchQuery;

use Iverberk\LarasearchQuery\Exceptions\NotAllowedException;

abstract class AbstractQuery {

	/**
	 * @var array
	 */
	protected $query;

	/**
	 * @var string
	 */
	protected $class;

	/**
	 * @param string $query
	 * @param $class
	 */
	function __construct($class, $query = null)
	{
		$this->class = $class;

		if (null != $query) {
			$this->setQuery($query);
		}
	}

	/**
	 * @return mixed
	 */
	abstract public function generate();

	/**
	 * @return array
	 */
	public function getQuery()
	{
		return $this->query;
	}

	/**
	 * @param string $query
	 */
	public function setQuery($query)
	{
		$this->query = $this->parseQuery($query);
	}

	/**
	 * @param $query
	 * @return array
	 */
	protected function parseQuery($queryString)
	{
		$query = [];

		// Split on |
		$parts = $this->splitString($queryString, '|');

		foreach($parts as $part)
		{
			$queryPart = [];

			// Determine field
			$fieldValue = $this->splitString($part, '::', 2);

			$field = count($fieldValue) == 2 ? $fieldValue[0] : '_all';
			$valueString = count($fieldValue) == 2 ? $fieldValue[1] : $fieldValue[0];

			// Split on ,
			$values = $this->splitString($valueString, ',');
			foreach ($values as $value)
			{
				if (empty($value) || '-' == $value)
				{
					throw new NotAllowedException('Empty query part found');
				}

				if (strpos($value, '-') === 0)
				{
					if (isset($queryPart['+']))
					{
						throw new NotAllowedException('Can not mix positive and negative searches in or clause.');
					}

					$queryPart['-'][] = substr($value, 1);
				} else
				{
					if (isset($queryPart['-']))
					{
						throw new NotAllowedException('Can not mix positive and negative searches in or clause.');
					}

					$queryPart['+'][] = preg_replace('/^\\\-/', '-', $value);
				}
			}

			$query[$field][] = $queryPart;
		}

		return $query;
	}

	protected function splitString($string, $delimiter = ',', $limit = -1)
	{
		$parts = preg_split('~(?<!\\\)' . preg_quote($delimiter, '~') . '~', $string, $limit);

		return str_replace('\\' . $delimiter, $delimiter, $parts);
	}
}