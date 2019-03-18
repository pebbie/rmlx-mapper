<?php

namespace Rmlx\SourceHandler;

use Rmlx\RmlContext;

class EmptySource extends RmlSource {
	function __construct() {
	}

	public function open($location) {
	}

	public function iterate($iterator, $ref=null) {
		yield new RmlContext();
	}

	public function lookup($reference, $ref=null) {
		return null;
	}
}

?>