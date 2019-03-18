<?php

namespace Rmlx\Component;

class IRITemplatePredicateMapper implements BaseMapperComponent {

	private $src;

	function __construct($src){
		$this->src = $src;
	}

	public function map(&$context){
		$pval = "<".$context->apply($this->src).">";
		$context->put("__predicate__", $pval);
	}
}

?>