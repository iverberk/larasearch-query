<?php namespace Iverberk\LarasearchQuery;

class ElasticsearchQuery extends AbstractQuery {

	/**
	 * @return mixed
	 */
	public function generate()
	{
		$queryPart = $this->query ? $this->generateBoolQuery() : $this->generateMatchAllQuery();

		$esQuery['sort'] = $this->sort ?: [];
		$esQuery['from'] = ($this->page - 1) * $this->perPage;
		$esQuery['size'] = $this->perPage;

		$esQuery['query']['filtered'] = [
			'query' => $queryPart
		];

		return $esQuery;
	}

	/**
	 * Generate an elasticsearch bool query based on the current query
	 *
	 * @return array
	 */
	private function generateBoolQuery()
	{
		$must = [];
		$mustNot = [];

		foreach ($this->query as $section)
		{
			$aliases = [];
			foreach ($section['fields'] as $field)
			{
				$aliases = array_merge($aliases, $this->getFieldAliases($field));
			}

			foreach ($section['query'] as $posNeg => $values)
			{
				$fields = $this->getFieldsInclAnalyzers($aliases);

				$disMaxQueries = [];

				foreach($values as $value)
				{
					$disMaxQueries[] = [
						'multi_match' => [
							'query' => strtolower($value),
							'fields' => $fields,
							'type' => 'phrase',
							'analyzer' => 'keyword'
						]
					];
				}

				$queryPart['dis_max']['queries'] = $disMaxQueries;
			}

			if ('+' == $posNeg)
			{
				$must[] = $queryPart;
			}
			else
			{
				$mustNot[] = $queryPart;
			}
		}

		$boolQuery = [
			'bool' => [
				'must' => $must,
				'must_not' => $mustNot
			]
		];

		return $boolQuery;
	}

	/**
	 * Get the aliases for a search param
	 *
	 * @param $field the name of the search param
	 *
	 * @return array of field names
	 */
	private function getFieldAliases ($field)
	{
		$aliases  = [];

		$klass = $this->class;

		if (property_exists($klass, '__es_config'))
		{
			$esConfig = $klass::$__es_config;
			if (isset($esConfig['fieldAliases'][$field]))
			{
				$aliases = $esConfig['fieldAliases'][$field];
			}
			else
			{
				array_push($aliases, $field);
			}
		}

		return $aliases;
	}

	/**
	 * Construct a list of fieldnames with all used analyzers for these fields
	 *
	 * @param array $fields list of fieldnames
	 *
	 * @return array fieldnames including al used analyzers
	 */
	private function getFieldsInclAnalyzers(array $fields)
	{
		$analyzers = [];

		foreach($fields as $fieldName)
		{
			if ('_all' == $fieldName)
			{
				array_push($analyzers, $fieldName);
			}
			else
			{
				array_push($analyzers, $fieldName, $fieldName . ".analyzed");

				$analyzerConfig = $this->getAnalyzerConfig();

				foreach ($analyzerConfig as $analyzer => $attributes)
				{
					if (in_array($fieldName, $attributes))
					{
						array_push($analyzers, $fieldName . ".${analyzer}");
					}
				}
			}
		}

		return $analyzers;
	}

	/**
	 * Get the config for all used analyzers
	 *
	 * @return array list of analyzers with corresponding fieldnames
	 */
	private function getAnalyzerConfig()
	{
		$analyzers = [];

		$klass = $this->class;

		if (property_exists($klass, '__es_config'))
		{
			$esConfig = $klass::$__es_config;

			if (isset($esConfig['analyzers']))
			{
				$analyzers = $esConfig['analyzers'];
			}
		}

		return $analyzers;
	}

	/**
	 * @return array
	 */
	private function generateMatchAllQuery()
	{
		return [
			'match_all' => []
		];
	}

	/**
	 *
	 */
	private function generateFilter() {

	}

}