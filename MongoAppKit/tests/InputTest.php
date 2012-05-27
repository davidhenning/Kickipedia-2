<?php

namespace MongoAppKit;

class InputTest extends \PHPUnit_Framework_TestCase {

	public function testSanitizeTrim() {
		$input = new Input();
		$value = ' value ';
		$expectedValue = trim($value);
		$sanitizedValue = $input->sanitize($value);

		$this->assertEquals($expectedValue, $sanitizedValue);
	}

	public function testSanitizeUrlDecode() {
		$input = new Input();
		$value = rawurlencode('value [] =');
		$expectedValue = rawurldecode($value);
		$sanitizedValue = $input->sanitize($value);

		$this->assertEquals($expectedValue, $sanitizedValue);
	}

	public function testSanitizeStripTags() {
		$input = new Input();
		$value = '<b>strong</ba>';
		$expectedValue = strip_tags($value);
		$sanitizedValue = $input->sanitize($value);

		$this->assertEquals($expectedValue, $sanitizedValue);
	}

	public function testSanitizeSpecialChars() {
		$input = new Input();
		$value = '<b>strong</ba>';
		$expectedValue = htmlspecialchars($value);
		$sanitizedValue = $input->sanitize($value);

		$this->assertEquals($expectedValue, $sanitizedValue);
	}

}