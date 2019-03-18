<?php

namespace Rmlx\Component;

class ReferenceLiteralMapper extends ConstantLiteralMapper {

	function __construct($src){
		parent::__construct($src);
	}

	public function map(&$context){
		$val = '"'.$context->get($this->src).'"'.$this->decoration();
		$context->put("__object__", $val);
	}
}

?>