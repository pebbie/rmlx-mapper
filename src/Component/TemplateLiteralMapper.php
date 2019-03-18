<?php

namespace Rmlx\Component;

class TemplateLiteralMapper extends ConstantLiteralMapper {

	private $src;

	function __construct($src){
		$this->src = $src;
		parent::__construct($src);
	}

	public function map(&$context){
		$val = '"'.$context->apply($this->src).'"'.$this->decoration();
		$context->put("__object__", $val);
	}
}

?>