<?php

namespace Rmlx\SourceHandler;

abstract class RmlSource {
	
    public function setup($context)
    {
        //the default setup is just open
        $this->open($context->get("__source__"));
    }

    public function open($location)
    {
        throw new EasyRdf_Exception(
            "This method should be overridden by sub-classes."
        );
    }

    public function iterate($iterator, $ref=null)
    {
        throw new EasyRdf_Exception(
            "This method should be overridden by sub-classes."
        );
    }

    public function lookup($reference, $ref=null)
    {
        throw new EasyRdf_Exception(
            "This method should be overridden by sub-classes."
        );
    }
}

?>