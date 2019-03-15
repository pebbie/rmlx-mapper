<?php

use Rmlx\map_vars;

class IRITemplateSubjectMapper implements BaseMapperComponent {

	private $src;

	function __construct($src, $isBlank=false){
		$this->src = $src;
	}

	public function map($context){
		$context->put("__subject__", "<".$context->apply($this->src).">");
	}
}

?>