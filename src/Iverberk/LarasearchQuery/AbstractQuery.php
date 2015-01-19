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
	 * @var array
	 */
	protected $sort;

	/**
	 * @var integer
	 */
	protected $page = 1;

	/**
	 * @var integer
	 */
	protected $perPage = 50;

	/**
	 * @param $class
	 * @param string $query
	 * @param null $sort
	 * @param null $page
	 * @param null $perPage
	 */
	function __construct($class, $query = null, $sort = null, $page = null, $perPage = null)
	{
		$this->class = $class;

		if ($query) $this->setQuery($query);
		if ($sort) $this->setSort($sort);
		if ($page) $this->page = $page;
		if ($perPage) $this->perPage = $perPage;
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
	 * @return null
	 */
	public function getSort()
	{
		return $this->sort;
	}

	/**
	 * @param null $sort
	 */
	public function setSort($sort)
	{
		$this->sort = $this->parseSort($sort);
	}

	/**
	 * @param $queryString
	 * @throws NotAllowedException
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

	/**
	 * @param $sortString
	 * @return array
	 */
	private function parseSort($sortString)
	{
		$sort = [];
		$fields = $this->splitString($sortString, ',');

		foreach($fields as $field)
		{
			if (strpos($field, '-') === 0)
			{
				$sort[substr($field, 1)] = ['order' => 'desc'];
			}
			else
			{
				$sort[$field] = ['order' => 'asc'];
			}
		}

		return $sort;
	}

	/**
	 * @param $string
	 * @param string $delimiter
	 * @param int $limit
	 * @return mixed
	 */
	protected function splitString($string, $delimiter = ',', $limit = -1)
	{
		$parts = preg_split('~(?<!\\\)' . preg_quote($delimiter, '~') . '~', $string, $limit);

		return str_replace('\\' . $delimiter, $delimiter, $parts);
	}

}

