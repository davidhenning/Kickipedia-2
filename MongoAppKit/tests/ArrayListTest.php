<?php

use MongoAppKit\Lists\ArrayList;

class ArrayListTest extends \PHPUnit_Framework_TestCase {

	public function testAssign() {
		$array = array('food' => 'shrimps', 'sauce' => 'cocktail');
		$list = new ArrayList();
		$list->assign($array);
		
		$this->assertEquals($array, $list->getProperties());
	}

	public function testProperty() {
		$value = 'bar';
		$list = new ArrayList();
		$list->setProperty('foo', $value);

		$this->assertEquals($value, $list->getProperty('foo'));
	}

	public function testNonExistingProperty() {
		try {
			$list = new ArrayList();
			$value = $list->getProperty('foo');
		} catch(\OutOfBoundsException $e) {
			return;
		}

		$this->fail('Expected OutOfBoundsException was not thrown.');
	}

	public function testRemoveProperty() {
		try {
			$property = 'foo';
			$value = 'bar';
			$list = new ArrayList();
			$list->setProperty($property, $value);
			$list->removeProperty($property);
			$value = $list->getProperty($property);
		} catch(\OutOfBoundsException $e) {
			return;
		}

		$this->fail('Expected OutOfBoundsException was not thrown.');
	}

	public function testRemoveNonExistingProperty() {
		try {
			$list = new ArrayList();
			$list->removeProperty('foo');
		} catch(\OutOfBoundsException $e) {
			return;
		}

		$this->fail('Expected OutOfBoundsException was not thrown.');
	}
}