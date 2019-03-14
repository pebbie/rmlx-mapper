<?php

use PHPUnit\Framework\TestCase;
use Rmlx\SourceHandler\PDOSource;

class PDOSourceTest extends TestCase {

	public function testInstantiation() {

		$this->assertInstanceOf(
			PDOSource::class,
			new PDOSource()
		);
	}

}