<?php

namespace Rmlx\Component;

use EasyRdf_Graph;

class GraphSinkComponent implements BaseSinkComponent {
	private $graph;

	function __construct(){
		$this->graph = new EasyRdf_Graph();
	}

	public function add($subject, $predicate, $object, $graph=null){
		$this->graph->add($subj, $pred, $obj);
	}

	public function close($context){
		//serialize
		//$this->graph->serialize($context->get("output_file"), $context->get("output_format"));
	}
}

?>