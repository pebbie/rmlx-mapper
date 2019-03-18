<?php

namespace Rmlx\Component;

class TemplateLiteralMapper extends ConstantLiteralMapper {

	function __construct($src){
		parent::__construct($src);
	}

	public function map(&$context){
		$val = '"'.$context->apply($this->src).'"'.$this->decoration();
		$context->put("__object__", $val);
	}
}

?>