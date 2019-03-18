<?php

namespace Rmlx\Component;

use EasyRdf_Graph;
use EasyRdf_Literal;

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

		$bp = strpos($object, "<");
		//echo var_export($object).$bp."\n";
		if($bp !== false && $bp==0)
			$obj = $this->graph->resource($object);
		else
			$obj = parse_nt_literal($object);
		
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

function unquote($tmp){
	return substr($tmp, 1, strlen($tmp)-2);
}

function parse_nt_literal($lv){
	$tp = strpos($lv, "^^");
	$lp = strpos($lv, "@");
	$lval = $lv;
	$lang = null;
	$dt = null;
	if($tp)
	{
		$lval = substr($lv, 1, $tp-2);
		$dt = unquote(substr($lv, $tp+2));
		$lang = null;
	}
	else if($lp)
	{
		$lval = substr($lv, 0, $lp);
		$lang = substr($lv, $lp+1);
		$dt = null;
	}

	return EasyRdf_Literal::create($lval, $lang, $dt);
}

?>