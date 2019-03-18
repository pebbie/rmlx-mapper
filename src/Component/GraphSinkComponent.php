<?php

namespace Rmlx\Component;

use EasyRdf_Graph;

class GraphSinkComponent implements BaseSinkComponent {
	private $graph;

	function __construct(){
		$this->graph = new EasyRdf_Graph();
	}

	public function open($context){
	}

	public function add($subject, $predicate, $object, $graph=null){
		$subj = $this->graph->resource($subject);
		$pred = $this->graph->resource($predicate);
		$obj = $this->graph->resource($object);
		$this->graph->add($subj, $pred, $obj);
	}

	public function close($context){
		//serialize
		$output = $this->graph->serialise($context->get("output_format"));
		$output_file = $context->get("output_file");
		if($output_file != "con")
			file_put_contents($output_file, $output);
		else
			echo $output;

	}
}

?>