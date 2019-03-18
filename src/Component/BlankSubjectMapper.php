<?php

namespace Rmlx\Component;

class BlankSubjectMapper implements BaseMapperComponent {

	private $src;

	function __construct(){
		$this->src = uniqid("B");
	}

	public function map(&$context){
		$val = "_:".$this->src;
		$context->put("__subject__", $val);
	}
}

?>