<?php

use Iverberk\LarasearchQuery\ElasticsearchQuery;

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
							],
							'must_not' => []
						]
					]
				]
			]
		];

		$query = new ElasticsearchQuery($model, $queryString);

		$this->assertEquals($expected, $query->generate());
	}

	/**
	 * @test
	 */
	public function it_should_generate_or_query_on_all_with_multiple_terms()
	{
		$model = 'Eur\Ods\Bod\Emmployee\Models\Employee';
		$queryString = 'ivo,ferry';
		$expected = [
			'query' => [
				'filtered' => [
					'query' => [
						'bool' => [
							'must' => [
								[
									'terms' => [
										'_all' => ['ivo', 'ferry']
									]
								]
							],
							'must_not' => []
						]
					]
				]
			]
		];

		$query = new ElasticsearchQuery($model, $queryString);

		$this->assertEquals($expected, $query->generate());
	}

	/**
	 * @test
	 */
	public function it_should_generate_query_on_all_with_lowercased_term()
	{
		$model = 'Eur\Ods\Bod\Emmployee\Models\Employee';
		$queryString = 'Ivo';
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
							],
							'must_not' => []
						]
					]
				]
			]
		];

		$query = new ElasticsearchQuery($model, $queryString);

		$this->assertEquals($expected, $query->generate());
	}

	/**
	 * @test
	 */
	public function it_should_generate_and_query_on_all_with_multiple_terms()
	{
		$model = 'Eur\Ods\Bod\Emmployee\Models\Employee';
		$queryString = 'ivo|ferry';
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
								],
								[
									'terms' => [
										'_all' => ['ferry']
									]
								]
							],
							'must_not' => []
						]
					]
				]
			]
		];

		$query = new ElasticsearchQuery($model, $queryString);

		$this->assertEquals($expected, $query->generate());
	}

	/**
	 * @test
	 */
	public function it_should_generate_query_on_all_with_multiple_lowercased_terms()
	{
		$model = 'Eur\Ods\Bod\Emmployee\Models\Employee';
		$queryString = 'Ivo,Ferry';
		$expected = [
			'query' => [
				'filtered' => [
					'query' => [
						'bool' => [
							'must' => [
								[
									'terms' => [
										'_all' => ['ivo', 'ferry']
									]
								]
							],
							'must_not' => []
						]
					]
				]
			]
		];

		$query = new ElasticsearchQuery($model, $queryString);

		$this->assertEquals($expected, $query->generate());
	}

	/**
	 * @test
	 */
	public function it_should_generate_and_query_on_initials_with_single_term()
	{
		$model = 'Eur\Ods\Bod\Emmployee\Models\Employee';
		$queryString = 'initials::I';
		$expected = [
			'query' => [
				'filtered' => [
					'query' => [
						'bool' => [
							'must' => [
								[
									'multi_match' => [
										'query' => ['I'],
										'fields' => ['initials', 'initials.analyzed']
									]
								]
							],
							'must_not' => []
						]
					]
				]
			]
		];

		$query = new ElasticsearchQuery($model, $queryString);

		$this->assertEquals($expected, $query->generate());
	}
}