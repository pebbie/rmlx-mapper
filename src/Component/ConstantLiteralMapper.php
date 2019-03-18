<?php

namespace Rmlx\Component;

use EasyRdf_Namespace as NS;
use EasyRdf_Resource;

class ConstantLiteralMapper implements BaseMapperComponent {

	private $src;

	protected $lang;

	protected $dtype;

	function __construct($src){
		$this->src = $src;
		$this->lang = null;
		$this->dtype = null;
	}

	public function set_language($lang){
		$this->lang = $lang;
	}

	public function set_datatype($dtype){
		$this->dtype = $dtype;
	}

	public function decoration(){
		if($this->dtype != null){
			if(is_a($this->dtype, EasyRdf_Resource)){
				$tpref = $this->dtype->prefix();
				if($tpref == "xsd")
					$val .= "^^".$this->dtype->getUri();
			}
		}
		else if($this->lang != null){
			$val .= "@".$this->lang;
		}
	}

	public function map(&$context){
		$val = '"'.$this->src.'"'.$this->decoration();
		$context->put("__object__", $val);
	}
}

?>