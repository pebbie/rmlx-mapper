<?php

use PHPUnit\Framework\TestCase;
use Rmlx\SourceHandler\JSONSource;

class JSONSourceTest extends TestCase {

	public function testInstantiation() {

		$this->assertInstanceOf(
			JSONSource::class,
			new JSONSource()
		);
	}

}