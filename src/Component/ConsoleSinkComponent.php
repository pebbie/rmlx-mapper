<?php

namespace Rmlx\Component;

class ConsoleSinkComponent implements BaseSinkComponent {
	function __construct(){
	}

	public function open($context){}
	public function close($context){}

	public function add($subject, $predicate, $object, $graph=null){
		echo "$subject $predicate $object $graph\n";
	}
}

?>