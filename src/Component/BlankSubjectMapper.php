<?php

use Rmlx\map_vars;

class BlankSubjectMapper implements BaseMapperComponent {

	private $src;

	function __construct(){
		$this->src = uniqid("B");
	}

	public function map($context){
		$context->put("__subject__", "_:".$this->src);
	}
}

?>