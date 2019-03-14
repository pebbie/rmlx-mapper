<?php

use PHPUnit\Framework\TestCase;
use Rmlx\SourceHandler\CSVSource;

class CSVSourceTest extends TestCase {

	public function testInstantiation() {

		$this->assertInstanceOf(
			CSVSource::class,
			new CSVSource()
		);
	}

}