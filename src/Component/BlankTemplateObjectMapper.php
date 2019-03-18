<?php

namespace Rmlx\Component;

class BlankTemplateObjectMapper implements BaseMapperComponent {

	private $src;

	function __construct($val=null){
		$this->src = $val;
	}

	public function map(&$context){
		if($this->src==null)
			$val = "_:".uniqid("B");
		else
			$val = "_:".$context->apply($this->src);
		$context->put("__object__", $val);
	}
}

?>