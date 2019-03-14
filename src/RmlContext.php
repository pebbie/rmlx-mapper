<?php

namespace Rmlx;

interface Context {
	public function get($key);
	public function apply($template);
}

class RmlContext implements Context {
	//primary key-value mapping that this object holds
	private $variables;
	//key-value containing delegate context (might be source's cursor)
	private $delegates;

	//
	public static $default_notfound = null;

	function __construct($vars=array()){
		$this->variables = $vars;
		$this->delegates = array();
	}

	public function get($key){
		echo "looking for ".$key."\n";
		if(array_key_exists($key, $this->variables))
			return $this->variables[$key];
		else foreach($this->delegates as $del){
			$res = $del->get($key);
			if($res != RmlContext::$default_notfound){
				return $res;
			}
		}
		return RmlContext::$default_notfound;
	}

	public function put($key, &$value){
		$this->variables[$key] = $value;
	}

	public function push_context($context){
		$this->delegates[] = $context;
	}

	public function pop_context($context){
		array_splice($delegates, -1, 1);
	}

	public function apply($template){
	    $out = $template;
	    foreach(extract_vars($template) as $var){
	        $evar = (substr($var,0,1)=='!')?substr($var, 1):$var;
	        $val = $this->get($var);
	        if($val != RmlContext::$default_notfound)
	        {
	            if(strlen($evar)==strlen($var))
	                $out = str_replace("{".$var."}", urlencode($val), $out);
	            else
	                $out = str_replace("{".$var."}", $val, $out);//raw values
	        }
	        else
	        {
	            return "";
	        }
	    }
	    return $out;
	}
}

function extract_vars($template)
{
    $out = array();
    $tmp = explode("{", $template);
    unset($tmp[0]);
    foreach($tmp as $value)
    {
        $out[] = substr($value, 0, strpos($value,"}"));
    }
    return $out;
}

?>