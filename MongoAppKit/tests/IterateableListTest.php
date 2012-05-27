<?php

use MongoAppKit\Lists\IterateableList;

class IterateableListTest extends \PHPUnit_Framework_TestCase {

	public function testCount() {
		$list = new IterateableList();
		$list->setProperty('foo', 'bar');

		$this->assertEquals(1, count($list));
	}

	public function testIteration() {
		$array = array('food' => 'shrimps', 'sauce' => 'cocktail');
		$list = new IterateableList();
		$list->assign($array);

		$newArray = array();

		foreach($list as $key => $value) {
			$newArray[$key] = $value;
		}

		$this->assertEquals($array, $newArray);
	}

	public function testArrayAccess() {
		$list = new IterateableList();
		$property = 'foo';
		$value = 'bar';
		$array = array('foo' => 'bar');
		$list[$property] = $value;

		$this->assertEquals($array[$property], $list[$property]);
	}

	public function testArrayAccessExists() {
		$list = new IterateableList();
		$this->assertFalse(isset($list['foo']));
	}

	public function testArrayAccessUnset() {
		$list = new IterateableList();
		$list['foo'] = 'bar';
		unset($list['foo']);

		$this->assertFalse(isset($list['foo']));
	}
}