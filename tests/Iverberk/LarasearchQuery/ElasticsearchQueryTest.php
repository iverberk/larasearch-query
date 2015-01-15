<?php

use Eur\Ods\Support\Query\ElasticsearchQuery;

class ElasticsearchQueryTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function it_should_generate_query_on_all_with_single_term()
	{
		$model = 'Eur\Ods\Bod\Emmployee\Models\Employee';
		$queryString = 'ivo';
		$expected = [
			'query' => [
				'filtered' => [
					'query' => [
						'bool' => [
							'must' => [
								[
									'terms' => [
										'_all' => ['ivo']
									]
								]
							]
						]
					]
				]
			]
		];

		$query = new ElasticsearchQuery($model, $queryString);

		$this->assertEquals($query->generate(), $expected);
	}

} 