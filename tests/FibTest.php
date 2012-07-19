<?php

require_once dirname(__FILE__) . '/../src/Fib.php';
require_once 'PHPUnit/Autoload.php';

class FibTest extends PHPUnit_Framework_TestCase {

	public function testFib() {

		$this->assertEquals(0, fib(0));
		$this->assertEquals(fib(5), fib(5-1)+fib(5-2));

	}

}