<?php

namespace Rmlx\Component;

class SourceTemplateMapper implements BaseMapperComponent {
	private $template;
	function __construct($template){
		$this->template = $template;
	}

	public function map(&$context){
		$value = $context->apply($this->template);
		$context->put("__source__", $value);
	}
}

?>