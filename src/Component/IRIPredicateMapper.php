<?php

namespace Rmlx\Component;

class IRIPredicateMapper implements BaseMapperComponent {

	private $src;

	function __construct($src){
		$this->src = $src;
	}

	public function map(&$context){
		$val = "<".$this->src.">";
		$context->put("__predicate__", $val);
	}
}

?>