<?php

use Rmlx\map_vars;

class SourceMapper implements BaseMapperComponent {

	private $src;

	function __construct($src){
		$this->src = $src;
	}

	public function map($context){
		$context->put("__source__", $this->src);
	}
}

?>