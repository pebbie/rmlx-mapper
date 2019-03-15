<?php

namespace Rmlx\Component;

class SourceMapper implements BaseMapperComponent {

	private $src;

	function __construct($src){
		$this->src = $src;
	}

	public function map(&$context){
		$context->put("__source__", $this->src);
	}
}

?>