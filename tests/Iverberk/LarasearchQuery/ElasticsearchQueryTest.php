<?php

use Iverberk\LarasearchQuery\ElasticsearchQuery;

class ElasticsearchQueryTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function it_should_generate_match_all_query_on_empty_input()
	{
		$model = 'Dummy';
		$queryString = null;
		$expected = [
			'query' => [
				'filtered' => [
					'query' => [
						'match_all' => []
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
		$queryString = 'field1::Ivo';
		$expected = [
			'query' => [
				'filtered' => [
					'query' => [
						'bool' => [
							'must' => [
								[
									'dis_max' => [
										'queries' => [
											[
												'multi_match' => [
													'query' => 'Ivo',
													'fields' => ['field1', 'field1.analyzed'],
													'type' => 'phrase'
												]
											]
										]
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
	public function it_should_generate_query_on_field_with_single_term_and_extra_analyzers()
	{
		$model = 'Dummy';
		$queryString = 'field2::Ivo';
		$expected = [
			'query' => [
				'filtered' => [
					'query' => [
						'bool' => [
							'must' => [
								[
									'dis_max' => [
										'queries' => [
											[
												'multi_match' => [
													'query' => 'Ivo',
													'fields' => ['field2', 'field2.analyzed', 'field2.word_start'],
													'type' => 'phrase'
												]
											]
										]
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
		$queryString = 'field1::Ivo|field2::Ferry';
		$expected = [
			'query' => [
				'filtered' => [
					'query' => [
						'bool' => [
							'must' => [
								[
									'dis_max' => [
										'queries' => [
											[
												'multi_match' => [
													'query' => 'Ivo',
													'fields' => ['field1', 'field1.analyzed'],
													'type' => 'phrase'
												]
											]
										]
									]
								],
								[
									'dis_max' => [
										'queries' => [
											[
												'multi_match' => [
													'query' => 'Ferry',
													'fields' => ['field2', 'field2.analyzed', 'field2.word_start'],
													'type' => 'phrase'
												]
											]
										]
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
	public function it_should_generate_query_on_field_with_multiple_terms()
	{
		$model = 'Dummy';
		$queryString = 'field1::Ivo,Ferry';
		$expected = [
			'query' => [
				'filtered' => [
					'query' => [
						'bool' => [
							'must' => [
								[
									'dis_max' => [
										'queries' => [
											[
												'multi_match' => [
													'query' => 'Ivo',
													'fields' => ['field1', 'field1.analyzed'],
													'type' => 'phrase'
												]
											],
											[
												'multi_match' => [
													'query' => 'Ferry',
													'fields' => ['field1', 'field1.analyzed'],
													'type' => 'phrase'
												]
											]
										]
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
	public function it_should_generate_and_or_query_on_field_with_multiple_terms()
	{
		$model = 'Dummy';
		$queryString = 'field1::Ivo,Ferry|field1::Lennert';
		$expected = [
			'query' => [
				'filtered' => [
					'query' => [
						'bool' => [
							'must' => [
								[
									'dis_max' => [
										'queries' => [
											[
												'multi_match' => [
													'query' => 'Ivo',
													'fields' => ['field1', 'field1.analyzed'],
													'type' => 'phrase'
												]
											],
											[
												'multi_match' => [
													'query' => 'Ferry',
													'fields' => ['field1', 'field1.analyzed'],
													'type' => 'phrase'
												]
											]
										]
									]
								],
								[
									'dis_max' => [
										'queries' => [
											[
												'multi_match' => [
													'query' => 'Lennert',
													'fields' => ['field1', 'field1.analyzed'],
													'type' => 'phrase'
												]
											]
										]
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