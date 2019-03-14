<?php

namespace Rmlx\Component;

class RootMapperComponent implements BaseMapperComponent {

	protected $children;

	function __construct(){
		$this->children = array();
	}

	public function add_mapper(&$child){
		$this->children[] = $child;
	}

	public function map(&$context){
		foreach($this->children as $child_mapper){
			$child_mapper->map($context);
		}
	}
}

?>