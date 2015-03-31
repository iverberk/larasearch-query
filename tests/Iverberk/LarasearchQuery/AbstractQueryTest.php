<?php

class AbstractQueryTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function it_should_parse_search_query_string_without_negation()
	{
		$test = 'ivo,ferry';
		$expected = [[
			'fields' => [
				'_all'
			],
			'query' => [
				'+' =>[
					'ivo',
					'ferry'
				]
			]
		]];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
		$this->assertEquals($expected, $query->getQuery());
	}

	/**
	 * @test
	 * @expectedException Iverberk\LarasearchQuery\Exceptions\NotAllowedException
	 */
	public function it_should_fail_on_search_query_string_with_mixed_negation()
	{
		$test = 'ivo,-ferry';

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
	}

	/**
	 * @test
	 */
	public function it_should_parse_search_query_string_with_escaped_delimiter()
	{
		$test = 'ivo\,ferry';

		$expected = [[
			'fields' => [
				'_all'
			],
			'query' => [
				'+' =>[
					'ivo,ferry'
				]
			]
		]];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
		$this->assertEquals($expected, $query->getQuery());
	}

	/**
	 * @test
	 */
	public function it_should_parse_search_query_string_with_escaped_and_unescaped_delimiter()
	{
		$test = 'ivo,ferry\,lennert';

		$expected = [[
			'fields' => [
				'_all'
			],
			'query' => [
				'+' =>[
					'ivo',
					'ferry,lennert'
				]
			]
		]];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
		$this->assertEquals($expected, $query->getQuery());
	}

	/**
	 * @test
	 */
	public function it_should_parse_search_query_string_with_escaped_negation()
	{
		$test = 'ivo,\-ferry';
		$expected = [[
			'fields' => [
				'_all'
			],
			'query' => [
				'+' =>[
					'ivo',
					'-ferry'
				]
			]
		]];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
		$this->assertEquals($expected, $query->getQuery());
	}

	/**
	 * @test
	 */
	public function it_should_parse_search_query_string_with_escaped_negation_and_slash_dash()
	{
		$test = 'ivo,\-fe\-rry';
		$expected = [[
			'fields' => [
				'_all'
			],
			'query' => [
				'+' =>[
					'ivo',
					'-fe\-rry'
				]
			]
		]];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
		$this->assertEquals($expected, $query->getQuery());
	}

	/**
	 * @test
	 */
	public function it_should_parse_search_query_string_with_negation_followed_by_dash()
	{
		$test = '-ivo,--ferry';
		$expected = [[
			'fields' => [
				'_all'
			],
			'query' => [
				'-' =>[
					'ivo',
					'-ferry'
				]
			]
		]];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
		$this->assertEquals($expected, $query->getQuery());
	}

	/**
	 * @test
	 * @expectedException Iverberk\LarasearchQuery\Exceptions\NotAllowedException
	 */
	public function it_should_fail_on_empty_value_part()
	{
		$test = 'ivo,,ferry';

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
	}

	/**
	 * @test
	 * @expectedException Iverberk\LarasearchQuery\Exceptions\NotAllowedException
	 */
	public function it_should_fail_on_negated_empty_value_part()
	{
		$test = 'ivo,-,ferry';

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
	}

	/**
	 * @test
	 */
	public function it_should_split_on_pipe_delimiter()
	{
		$test = 'ivo|ferry';
		$expected = [
			[
				'fields' => [
					'_all'
				],
				'query'  => [
					'+' => [
						'ivo'
					]
				]
			],
			[
				'fields' => [
					'_all'
				],
				'query'  => [
					'+' => [
						'ferry'
					]
				]
			]
		];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
		$this->assertEquals($expected, $query->getQuery());
	}

	/**
	 * @test
	 */
	public function it_should_split_on_pipe_delimiter_and_negated_value()
	{
		$test = 'ivo|-ferry';
		$expected = [
			[
				'fields' => [
					'_all'
				],
				'query'  => [
					'+' => [
						'ivo'
					]
				]
			],
			[
				'fields' => [
					'_all'
				],
				'query'  => [
					'-' => [
						'ferry'
					]
				]
			]
		];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
		$this->assertEquals($expected, $query->getQuery());
	}

	/**
	 * @test
	 */
	public function it_should_not_split_on_escaped_pipe_delimiter()
	{
		$test = 'ivo\|ferry';
		$expected = [[
			'fields' => [
				'_all'
			],
			'query' => [
				'+' =>[
					'ivo|ferry'
				]
			]
		]];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
		$this->assertEquals($expected, $query->getQuery());
	}

	/**
	 * @test
	 */
	public function it_should_not_split_on_start_with_escaped_pipe_delimiter()
	{
		$test = '\|ferry';
		$expected = [[
			'fields' => [
				'_all'
			],
			'query' => [
				'+' =>[
					'|ferry'
				]
			]
		]];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
		$this->assertEquals($expected, $query->getQuery());
	}

	/**
	 * @test
	 * @expectedException Iverberk\LarasearchQuery\Exceptions\NotAllowedException
	 */
	public function it_should_fail_on_empty_query_part()
	{
		$test = 'ivo||ferry';

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
	}

	/**
	 * @test
	 * @expectedException Iverberk\LarasearchQuery\Exceptions\NotAllowedException
	 */
	public function it_should_fail_on_negated_empty_query_part()
	{
		$test = 'ivo|-|ferry';

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
	}

	/**
	 * @test
	 */
	public function it_should_split_on_double_colon()
	{
		$test = 'ivo::ferry';
		$expected = [[
			'fields' => [
				'ivo'
			],
			'query' => [
				'+' =>[
					'ferry'
				]
			]
		]];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
		$this->assertEquals($expected, $query->getQuery());
	}

	/**
	 * @test
	 */
	public function it_should_not_split_on_escaped_double_colon()
	{
		$test = 'ivo\::ferry';
		$expected = [[
			'fields' => [
				'_all'
			],
			'query' => [
				'+' =>[
					'ivo::ferry'
				]
			]
		]];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
		$this->assertEquals($expected, $query->getQuery());
	}

	/**
	 * @test
	 * @expectedException Iverberk\LarasearchQuery\Exceptions\NotAllowedException
	 */
	public function it_should_fail_on_empty_value_part_with_fieldname()
	{
		$test = 'ivo::';

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
	}

	/**
	 * @test
	 * @expectedException Iverberk\LarasearchQuery\Exceptions\NotAllowedException
	 */
	public function it_should_fail_on_negated_empty_value_part_with_fieldname()
	{
		$test = 'ivo::-';

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
	}

	/**
	 * @test
	 */
	public function it_should_split_on_pipe_and_double_colon()
	{
		$test = 'ivo::ferry|lennert';
		$expected = [
			[
				'fields' => [
					'ivo'
				],
				'query' => [
					'+' =>[
						'ferry'
					]
				]
			],
			[
				'fields' => [
					'_all'
				],
				'query' => [
					'+' =>[
						'lennert'
					]
				]
			]
		];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setQuery($test);
		$this->assertEquals($expected, $query->getQuery());
	}

	/**
	 * @test
	 */
	public function it_should_parse_sort_string()
	{
		$test = 'lastname,-firstname';
		$expected = [
			'lastname' => [
				'order' => 'asc'
			],
			'firstname' => [
				'order' => 'desc'
			]
		];

		$query = new \Iverberk\LarasearchQuery\ElasticsearchQuery('');
		$query->setSort($test);
		$this->assertEquals($expected, $query->getSort());
	}

}