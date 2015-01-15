<?php namespace Iverberk\LarasearchQuery;

class ElasticsearchQuery extends AbstractQuery {

	/**
	 * @return mixed
	 */
	public function generate()
	{
		$this->generateQuery();

		foreach($this->query as $attribute => $value)
		{
			$negativeField =  false;

			if (substr($attribute, 0, 1) === '-')
			{
				$negativeField = true;
				$attribute = substr($attribute, 1);
			}

			if ($attribute === '_all')
			{
				$queryPart = [
					'terms' => [
						$attribute => $value
					]
				];
			}
			else
			{
				$fields = [$attribute, $attribute . ".analyzed"];

				if (property_exists($this->class, '__es_config'))
				{
					$klass = $this->class;

					foreach($klass::$__es_config as $analyzer => $attributes)
					{
						if (in_array($attribute, $attributes))
						{
							$fields[] = $attribute . ".${analyzer}";
						}
					}
				}

				$queryPart = [
					'multi_match' => [
						'query' => $value,
						'fields' => $fields
					]
				];
			}

			if ($negativeField)
			{
				$queryMustNot[] = $queryPart;
			}
			else
			{
				$queryMust[] = $queryPart;
			}
		}

		$query['query']['filtered'] = [
			'query' => [
				'bool' => [
					'must' => $queryMust,
					'must_not' => $queryMustNot
				]
			]
		];

		return $query;
	}

	private function generateQuery()
	{
		foreach ($this->query as $field => $x)
		{
			$must = [];
			$mustNot = [];
			foreach ($x as $y)
			{
				foreach ($y as $posNeg => $values)
				{
					foreach ($values as $value)
					{

					}
				}
			}


		}

		$query['query']['filtered'] = [
			'query' => [
				'bool' => [
					'must' => '',
					'must_not' => ''
				]
			]
		];
	}

	private function generateFilter() {}

}