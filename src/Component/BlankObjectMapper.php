<?php

namespace Rmlx\Component;

class BlankObjectMapper implements BaseMapperComponent {

	private $src;

	function __construct($val=null){
		if($val==null)
			$this->src = uniqid("B");
		else
			$this->src = $val;
	}

	public function map(&$context){
		$val = "_:".$this->src;
		$context->put("__object__", $val);
	}
}

?>