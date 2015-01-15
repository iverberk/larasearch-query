<?php namespace Iverberk\LarasearchQuery;

class ElasticsearchQuery extends AbstractQuery {

	/**
	 * @return mixed
	 */
	public function generate()
	{
		$boolQuery = $this->generateBoolQuery();

		$esQuery['query']['filtered'] = [
			'query' => [
				'bool' => $boolQuery
			]
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
						$queryPart = [
							'terms' => [
								$field => array_map('strtolower', $values)
							]
						];
					}
					else
					{
						$fields = [$field, $field . ".analyzed"];

						$klass = $this->class;

						foreach($klass::$__es_config as $analyzer => $attributes)
						{
							if (in_array($field, $attributes))
							{
								$fields[] = $field . ".${analyzer}";
							}
						}

						$disMaxQueries = [];

						foreach($values as $value)
						{
							$disMaxQueries[] = [
								'multi_match' => [
									'query' => $value,
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
		}

		$boolQuery = [
			'must' => $must,
			'must_not' => $mustNot
		];

		return $boolQuery;
	}

	private function generateFilter() {

	}

}