<?php namespace Iverberk\LarasearchQuery;

class ElasticsearchQuery extends AbstractQuery {

	/**
	 * @return mixed
	 */
	public function generate()
	{
		$queryPart = $this->query ? $this->generateBoolQuery() : $this->generateMatchAllQuery();

		$esQuery['query']['filtered'] = [
			'query' => $queryPart
		];

		return $esQuery;
	}

	private function generateBoolQuery()
	{
		$must = [];
		$mustNot = [];

		foreach ($this->query as $field => $parts)
		{
			foreach ($parts as $part)
			{
				foreach ($part as $posNeg => $values)
				{
					if ('_all' == $field)
					{
						$fields = [$field];
					}
					else
					{
						$fields = [$field, $field . ".analyzed"];

						$klass = $this->class;

						if (property_exists($klass, '__es_config'))
						{
							foreach ($klass::$__es_config as $analyzer => $attributes)
							{
								if (in_array($field, $attributes))
								{
									$fields[] = $field . ".${analyzer}";
								}
							}
						}
					}

					$disMaxQueries = [];

					foreach($values as $value)
					{
						$disMaxQueries[] = [
							'multi_match' => [
								'query' => strtolower($value),
								'fields' => $fields,
								'type' => 'phrase'
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
		}

		$boolQuery = [
			'bool' => [
				'must' => $must,
				'must_not' => $mustNot
			]
		];

		return $boolQuery;
	}

	private function generateMatchAllQuery()
	{
		return [
			'match_all' => []
		];
	}

	private function generateFilter() {

	}

}