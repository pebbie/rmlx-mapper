<?php

namespace Rmlx\Component;

class ReferenceLiteralMapper extends ConstantLiteralComponent {

	private $src;

	function __construct($src){
		$this->src = $src;
		parent::__construct();
	}

	public function map(&$context){
		$val = '"'.$context->get($this->src).'"'.$this->decoration();
		$context->put("__object__", $val);
	}
}

?>