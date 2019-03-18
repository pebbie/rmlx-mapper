<?php

namespace Rmlx\Component;

class InversePredicateObjectMapperComponent extends PredicateObjectMapperComponent {
	// one predicate
	private $pred_mapper;

	private $obj_mappers;

	function __construct($pmap=null, $omap=array()){
		$this->pred_mapper = $pmap;

		if(is_array($omap))
			$this->obj_mappers = $omap;
		else
			$this->obj_mappers = array($omap);
	}

	public function map(&$context){
		$graph = $context->get("__graph__");

		$subj = $context->get("__subject__");
		
		$this->pred_mapper->map($context);
		$pred = $this->context->get("__predicate__");

		foreach($this->obj_mappers as $omap){
			$omap->map($context);
			$obj = $context->get("__object__");

			$context->get("__mapper__")->add($obj, $pred, $subj, $graph);
		}
		
	}
}

?>