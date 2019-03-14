<?php

namespace Rmlx\Component;

class RootMapperComponent implements BaseMapperComponent {

	protected $children;

	protected $default;

	function __construct($default){
		$this->children = array();
		$this->default = $default;
	}

	public function add_mapper(&$child){
		$this->children[] = $child;
	}

	public function map(&$context){
		$context->push_context($this->default);
		foreach($this->children as $child_mapper){
			$child_mapper->map($context);
		}
	}
}

?>