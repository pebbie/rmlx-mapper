<?php

namespace Rmlx\SourceHandler;


class CSVSource extends RmlSource {

	private $csv;
    private $header;
    private $delimiter = ',';
    private $rowid;
    private $location;
    private $chunk_size = 1600;

    public function __construct()
    {
        
    }

    public function __destruct()
    {
        //if($this->csv) fclose($this->csv);
    }

    public function open($location, $ref=null)
    {
        $this->location = $location;
        /*
        global $content_cache;
        if(!array_key_exists($location, $content_cache))
        {
            $src_path = "tmp/";
            $content = file_get_contents($location);
            $csv_file = $src_path.md5($location).'-'.md5($content).".csv";
            file_put_contents($csv_file, $content);
            $content_cache[$location] = $csv_file;
        }
        */
        $this->csv = file_get_contents($location);
        //$fp = fopen("php://memory", "r+");
        //todo: replace file_get_contents
        //fputs($fp, get_content($location, ".csv"));
        //rewind($fp);       
        //$this->csv = $fp;        
        $this->rowid = 0;
    }

    public function iterate($iterator, $ref=null)
    {
        switch($iterator)
        {
            case ',':
            case ';':
            case '|':
            case '\t':
                $this->delimiter = $iterator;
                break;
        }
        
        $this->header = fgetcsv($this->csv, $this->chunk_size, $this->delimiter);
        foreach($this->header as $idx => $head)
        {
            $this->header[$idx] = str_replace(" ", "_", $head);
        }
        #print_r($this->header);
        $tmp = array();
        $this->rowid = 0;
        while(($row = fgetcsv($this->csv, $this->chunk_size, $this->delimiter)) != FALSE)
        {
            $ch = count($this->header);
            $cr = count($row);
            if ($ch != $cr && strlen(trim($row[$cr-1]))==0)
                unset($row[$cr-1]);
            #echo count($this->header);
            #print_r($row);
            if(count($this->header)!=count($row)){
                print_r($row);
                #break;
            }
            $this->rowid++;
            $tmp[] = array_combine($this->header, $row);
            $tmp[count($tmp)-1]['@row'] = $this->rowid;
        }
        //$this->row = $row;
        //return array();
        return $tmp;
    }

    public function lookup($reference, $ref=null)
    {
        $rr = str_replace(" ", "_", $reference);
        #print_r($ref);
        //echo $rr, $ref[$rr], array_key_exists($rr, $ref), "\n";
        if(array_key_exists($rr, $ref))
            return $ref[$rr];
        else return null;
    }

}