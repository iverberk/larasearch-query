<?php

use Iverberk\LarasearchQuery\ElasticsearchQuery;

class ElasticsearchQueryTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function it_should_generate_query_on_all_with_single_term()
	{
		$model = 'Dummy';
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
		$model = 'Dummy';
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
		$model = 'Dummy';
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
		$model = 'Dummy';
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
		$model = 'Dummy';
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
	public function it_should_generate_query_on_field_with_single_term()
	{
		$model = 'Dummy';
		$queryString = 'field1::I';
		$expected = [
			'query' => [
				'filtered' => [
					'query' => [
						'bool' => [
							'must' => [
								[
									'multi_match' => [
										'query' => ['I'],
										'fields' => ['field1', 'field1.analyzed']
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
	public function it_should_generate_query__on_field_with_single_term_and_extra_analyzers()
	{
		$model = 'Dummy';
		$queryString = 'field2::I';
		$expected = [
			'query' => [
				'filtered' => [
					'query' => [
						'bool' => [
							'must' => [
								[
									'multi_match' => [
										'query' => ['I'],
										'fields' => ['field2', 'field2.analyzed', 'field2.word_start']
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
	public function it_should_generate_and_query_on_fields_with_multiple_terms_and_extra_analyzers()
	{
		$model = 'Dummy';
		$queryString = 'field1::F|field2::I';
		$expected = [
			'query' => [
				'filtered' => [
					'query' => [
						'bool' => [
							'must' => [
								[
									'multi_match' => [
										'query' => ['F'],
										'fields' => ['field1', 'field1.analyzed']
									]
								],
								[
									'multi_match' => [
										'query' => ['I'],
										'fields' => ['field2', 'field2.analyzed', 'field2.word_start']
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