<?php

namespace Rmlx\SourceHandler;

class XMLSource extends RmlSource {
	function __construct() {
	}

	public function open($location) {
		echo $location."\n";
	}

	public function iterate($iterator, $ref=null) {
		yield new RmlContext();
	}

	public function lookup($reference, $ref=null) {
		return null;
	}
}

?>