<?php

require_once 'src/Set.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SetTest extends PHPUnit_Framework_TestCase {

	public function testExpand() {

		$data   = array('My', 'Array', 'To', 'Flatten');
		$flat   = Set::flatten($data);
		$result = Set::expand($flat);

		$this->assertEquals($data, $result);

		$data   = array(
			'0.Post.id'         => '1',
			'0.Post.title'      => 'First Post',
			'0.Author.id'       => '1',
			'0.Author.user'     => 'nate',
			'1.Post.id'         => '2',
			'1.Post.title'      => 'Second Post',
			'1.Author.id'       => '3',
			'1.Author.user'     => 'larry'
		);
		$result   = Set::expand($data);
		$expected = array(
			array(
				'Post'   => array(
					'id'        => '1',
					'title'     => 'First Post'
				),
				'Author' => array(
					'id'        => '1',
					'user'      => 'nate'
				),
			), array(
				'Post'      => array(
					'id'        => '2',
					'title'     => 'Second Post'
				),
				'Author' => array(
					'id'        => '3',
					'user'      => 'larry'
				)
			)
		);
		$this->assertEquals($expected, $result);

		$data     = array(
			'0/Post/id'     => 1,
			'0/Post/name'   => 'test post'
		);
		$result   = Set::expand($data, '/');
		$expected = array(
			array(
				'Post' => array(
					'id'    => 1,
					'name'  => 'test post'
				)
			)
		);
		$this->assertEquals($result, $expected);

	}

	public function testFlatten() {

		$post1 = array(
			'Post'      => array(
				'id'        => 1,
				'title'     => 'Foo'
			),
			'Author' => array(
				'id'        => 1,
				'name'      => 'Joe'
			)
		);

		$post2 = array(
			'Post'      => array(
				'id'        => 2,
				'title'     => 'Bar'
			),
			'Author' => array(
				'id'        => 1,
				'name'      => 'Joe'
			)
		);

		$output   = Set::flatten($post1);
		$expected = array(
			'Post.id'           => 1,
			'Post.title'        => 'Foo',
			'Author.id'         => 1,
			'Author.name'       => 'Joe',
		);
		$this->assertEquals($output, $expected);

		$output   = Set::flatten(array($post1, $post2), '-');
		$expected = array(
			'0-Post-id'         => 1,
			'0-Post-title'      => 'Foo',
			'0-Author-id'       => 1,
			'0-Author-name'     => 'Joe',
			'1-Post-id'         => 2,
			'1-Post-title'      => 'Bar',
			'1-Author-id'       => 1,
			'1-Author-name'     => 'Joe',
		);
		$this->assertEquals($output, $expected);

	}

	public function testMerge() {

		$result = Set::merge(array('foo'), array('bar'));
		$this->assertEquals($result, array('foo', 'bar'));

		$result = Set::merge(array('foo'), array('user' => 'bob', 'no-bar'), 'bar');
		$this->assertEquals($result, array('foo', 'user' => 'bob', 'no-bar', 'bar'));

		$a        = array('foo', 'foo2');
		$b        = array('bar', 'bar2');
		$expected = array('foo', 'foo2', 'bar', 'bar2');
		$this->assertEquals($expected, Set::merge($a, $b));

		$a        = array('foo' => 'bar', 'bar' => 'foo');
		$b        = array('foo' => 'no-bar', 'bar' => 'no-foo');
		$expected = array('foo' => 'no-bar', 'bar' => 'no-foo');
		$this->assertEquals($expected, Set::merge($a, $b));

		$a        = array('users' => array('bob', 'jim'));
		$b        = array('users' => array('lisa', 'tina'));
		$expected = array('users' => array('bob', 'jim', 'lisa', 'tina'));
		$this->assertEquals($expected, Set::merge($a, $b));

		$a        = array('users' => array('jim', 'bob'));
		$b        = array('users' => 'none');
		$expected = array('users' => 'none');
		$this->assertEquals($expected, Set::merge($a, $b));

		$a        = array('users' => array('lisa' => array('id' => 5, 'pw' => 'secret')), 'cakephp');
		$b        = array('users' => array('lisa' => array('pw' => 'new-pass', 'age' => 23)), 'ice-cream');
		$expected = array(
			'users' => array('lisa' => array('id' => 5, 'pw' => 'new-pass', 'age' => 23)),
			'cakephp',
			'ice-cream'
		);
		$result = Set::merge($a, $b);
		$this->assertEquals($expected, $result);

		$c = array(
			'users' => array(
				'lisa' => array(
					'pw' => 'you-will-never-guess',
					'age' => 25,
					'pet' => 'dog'
				)
			),
			'chocolate'
		);
		$expected = array(
			'users' => array(
				'lisa' => array(
					'id'  => 5,
					'pw'  => 'you-will-never-guess',
					'age' => 25,
					'pet' => 'dog'
				)
			),
			'cakephp',
			'ice-cream',
			'chocolate'
		);
		$this->assertEquals($expected, Set::merge($a, $b, $c));
		$this->assertEquals($expected, Set::merge($a, $b, array(), $c));

		$a  = array(
			'Tree',
			'CounterCache',
			'Upload' => array(
				'folder' => 'products',
				'fields' => array(
					'image_1_id',
					'image_2_id',
					'image_3_id',
					'image_4_id',
					'image_5_id'
				)
			)
		);
		$b = array(
			'Cacheable' => array(
				'enabled' => false
			),
			'Limit',
			'Bindable',
			'Validator',
			'Transactional'
		);
		$expected = array(
			'Tree',
			'CounterCache',
			'Upload' => array(
				'folder' => 'products',
				'fields' => array(
					'image_1_id',
					'image_2_id',
					'image_3_id',
					'image_4_id',
					'image_5_id'
				)
			),
			'Cacheable' => array(
				'enabled' => false
			),
			'Limit',
			'Bindable',
			'Validator',
			'Transactional'
		);
		$this->assertEquals(Set::merge($a, $b), $expected);

	}

}
