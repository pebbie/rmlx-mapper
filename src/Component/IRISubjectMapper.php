<?php

namespace Rmlx\Component;

class IRISubjectMapper implements BaseMapperComponent {

	private $src;

	function __construct($src){
		$this->src = $src;
	}

	public function map(&$context){
		$val = "<".$this->src.">";
		$context->put("__subject__", $val);
	}
}

?>