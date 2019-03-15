<?php

namespace Rmlx\SourceHandler;

abstract class RmlSource {
	
    public function setup($context)
    {
        //the default setup is just open
        //TODO:caching
        $source_path = $context->get("__source__");
    
        //append src_dir if no protocol scheme provided
        if(    substr( $source_path, 0, 4 ) != "http" 
            && substr( $source_path, 0, 4 ) != "data"
            && substr( $source_path, 0, 4 ) != "file"
        )
        $source_path = $context->get("src_dir").$source_path;

        echo "opening: ".$source_path."\n";
        $this->open($source_path);
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