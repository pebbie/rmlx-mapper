<?php

use Rmlx\map_vars;

class BlankTemplateSubjectMapper implements BaseMapperComponent {

	private $src;

	function __construct($src, $isBlank=false){
		$this->src = $src;
	}

	public function map($context){
		$context->put("__subject__", "_:".$context->apply($this->src));
	}
}

?>