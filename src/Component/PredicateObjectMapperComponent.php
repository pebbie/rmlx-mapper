<?php

namespace Rmlx\Component;

class PredicateObjectMapperComponent extends RootMapperComponent {
	// one predicate
	protected $pred_mapper;

	protected $obj_mappers;

	function __construct($pmap=null, $omap=array()){
		$this->pred_mapper = $pmap;

		if(is_array($omap))
			$this->obj_mappers = $omap;
		else
			$this->obj_mappers = array($omap);
	}

	public function add_object_mapper($omap){
		if(is_array($omap))
			foreach($omap as $om)
				$this->obj_mappers[] = $om;
		else
			$this->obj_mappers[] = $omap;
	}

	public function map(&$context){
		$graph = $context->get("__graph__");

		$subj = $context->get("__subject__");
		
		$this->pred_mapper->map($context);
		$pred = $context->get("__predicate__");

		foreach($this->obj_mappers as $omap){
			$omap->map($context);
			$obj = $context->get("__object__");

			$context->get("__mapper__")->add($subj, $pred, $obj, $graph);
		}
		
	}
}

?>