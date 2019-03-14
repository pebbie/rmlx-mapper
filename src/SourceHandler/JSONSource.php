<?php

namespace Rmlx\SourceHandler;

use Rmlx\RmlSource;

class JSONSource extends RmlSource {

	private $json;

    public function __construct()
    {
    }

    public function open($location)
    {
        //$this->json = $this->parser->decode(get_content($location, ".json"));
        $this->json = json_decode(get_content($location, ".json"), true);

    }

    public function iterate($iterator, $ref=null)
    {
        $tmp = jsonPath($this->json, $iterator);
        //print_r($tmp);
        if($tmp===false)
            return array($this->json);
        if ($ref==null)
            return jsonPath($this->json, $iterator);
        else
            return jsonPath($ref, $iterator);
    }

    public function lookup($reference, $ref=null)
    {
        if ($ref==null || $ref===false)
            return jsonPath($this->json, $reference);
        else
            return jsonPath($ref, $reference);
    }

}

?>