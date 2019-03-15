<?php

use Rmlx\map_vars;

class IRISubjectMapper implements BaseMapperComponent {

	private $src;

	function __construct($src){
		$this->src = $src;
	}

	public function map($context){
		$context->put("__subject__", "<".$this->src.">");
	}
}

?>