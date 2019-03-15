<?php

use Rmlx\map_vars;

class IRIPredicateMapper implements BaseMapperComponent {

	private $src;

	function __construct($src){
		$this->src = $src;
	}

	public function map($context){
		$context->put("__predicate__", "<".$this->src.">");
	}
}

?>