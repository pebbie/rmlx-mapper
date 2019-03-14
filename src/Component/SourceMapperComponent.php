<?php

namespace Rmlx\Component;

use Rmlx\SourceHandler;

class SourceMapperComponent implements BaseMapperComponent {

	private $iterator;

	private $processors;

	function __construct($it=null){
		$this->iterator = $it;
		$this->processors = array();
	}

	public function set_iterator($iterator){
		$this->iterator = $iterator;
	}

	public function get_iterator(){
		return $this->iterator;
	}

	public function add_processor($proc){
		$this->processors[] = $proc;
 	}

	function map(&$context){
		//run all processors (e.g. source template mapper)
		foreach($this->processors as $p)
			$p->map($context);
		//this is actually setup and return the SourceHandler instances
		$this->iterator->setup($context);
	}

}

?>