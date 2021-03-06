<?php

namespace Rmlx\Component;

class IRITemplateSubjectMapper implements BaseMapperComponent {

	private $src;

	function __construct($src){
		$this->src = $src;
	}

	public function map(&$context){
		$sval = "<".$context->apply($this->src).">";
		$context->put("__subject__", $sval);
	}
}

?>