<?php

namespace Rmlx\Component;

class SPARQLSinkComponent implements BaseSinkComponent {
	function __construct(){
	}

	public function open($context){}
	public function close($context){}

	public function add($subject, $predicate, $object, $graph=null){
		//TODO: SPARQL INSERT
		echo "$subject $predicate $object $graph.\n";
	}
}

?>