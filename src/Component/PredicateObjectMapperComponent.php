<?php

namespace Rmlx\Component;

class PredicateObjectMapperComponent extends RootMapperComponent {
	// one predicate
	private $pred_mapper;

	private $obj_mappers;

	function __construct(){
		$this->pred_mapper = null;

		$this->obj_mappers = array();
	}

	public function map(&$context){
		$graph = $context->get("__graph__");

		$subj = $context->get("__subject__");
		
		$this->pred_mapper->map($context);
		$pred = $this->context->get("__predicate__");

		foreach($this->obj_mappers as $omap){
			$omap->map($context);
			$obj = $context->get("__object__");

			$context->get("__mapper__")->add($subj, $pred, $obj, $graph);
		}
		
	}
}

?>