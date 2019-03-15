<?php

namespace Rmlx\SourceHandler;

class PDOSource extends RmlSource {

	private $db = NULL;

    public function open($location)
    {
        try
        {
            $this->db = new PDO($location);
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }

    public function iterate($iterator, $ref=null)
    {
        $stmt = $this->db->prepare($iterator);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function lookup($reference, $ref=null)
    {
        if(!$ref) return "";
        if(array_key_exists($reference, $ref))
        {
            return $ref[$reference];
        }
    }

}