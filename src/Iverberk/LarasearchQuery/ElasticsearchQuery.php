<?php namespace Iverberk\LarasearchQuery;

class ElasticsearchQuery extends AbstractQuery {

	/**
	 * @var array
	 */
	private $esQuery;

	/**
	 * @return mixed
	 */
	public function generate()
	{
		$this->generateQuery();

//		foreach($this->query as $attribute => $value)
//		{
//			$negativeField =  false;
//
//			if (substr($attribute, 0, 1) === '-')
//			{
//				$negativeField = true;
//				$attribute = substr($attribute, 1);
//			}
//
//			if ($attribute === '_all')
//			{
//				$queryPart = [
//					'terms' => [
//						$attribute => $value
//					]
//				];
//			}
//			else
//			{
//				$fields = [$attribute, $attribute . ".analyzed"];
//
//				if (property_exists($this->class, '__es_config'))
//				{
//					$klass = $this->class;
//
//					foreach($klass::$__es_config as $analyzer => $attributes)
//					{
//						if (in_array($attribute, $attributes))
//						{
//							$fields[] = $attribute . ".${analyzer}";
//						}
//					}
//				}
//
//				$queryPart = [
//					'multi_match' => [
//						'query' => $value,
//						'fields' => $fields
//					]
//				];
//			}
//
//			if ($negativeField)
//			{
//				$queryMustNot[] = $queryPart;
//			}
//			else
//			{
//				$queryMust[] = $queryPart;
//			}
//		}
//
//		$query['query']['filtered'] = [
//			'query' => [
//				'bool' => [
//					'must' => $queryMust,
//					'must_not' => $queryMustNot
//				]
//			]
//		];

		return $this->esQuery;
	}

	private function generateQuery()
	{
		$must = [];
		$mustNot = [];

		foreach ($this->query as $field => $parts)
		{
			foreach ($parts as $part) {
				foreach ($part as $posNeg => $values) {
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
						$queryPart = [
							'multi_match' => [
								'query'
								$field => array_map('strtolower', $values)
							]
						];
					}

					if ('+' == $posNeg) {
						$must[] = $queryPart;
					}
					else {
						$mustNot[] = $queryPart;
					}
				}
			}
		}

		$this->esQuery['query']['filtered'] = [
			'query' => [
				'bool' => [
					'must' => $must,
					'must_not' => $mustNot
				]
			]
		];
	}

	private function generateFilter() {}

}