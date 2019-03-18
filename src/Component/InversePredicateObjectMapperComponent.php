<?php

namespace Rmlx\Component;

class InversePredicateObjectMapperComponent extends PredicateObjectMapperComponent {

	function __construct($pmap=null, $omap=array()){
		parent::__construct($pmap, $omap);
	}

	public function map(&$context){
		$graph = $context->get("__graph__");

		$subj = $context->get("__subject__");
		
		$this->pred_mapper->map($context);
		$pred = $context->get("__predicate__");

		foreach($this->obj_mappers as $omap){
			$omap->map($context);
			$obj = $context->get("__object__");

			$context->get("__mapper__")->add($obj, $pred, $subj, $graph);
		}
		
	}
}

?>