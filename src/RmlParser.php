<?php
/*
 RmlParser read a graph containing RML mapping description and build RmlMapperComponent
 */

namespace Rmlx;

use EasyRdf_Namespace as NS;

use Rmlx\Component;
use Rmlx\SourceHandler;

class RmlParser {
	
	private $graph;
	private $mapping;
	private $tvars;
	private $dependency;
	private $map_ordering;
	private $default_context;

	function __construct(){
		$this->force_namespace();

		$this->mapping = array();
		$this->tvars = array();
		$this->dependency = array();
		$this->map_ordering = array();
		$this->default_context = new RmlContext();
	}

	public function parse(&$graph) {
		$this->graph = $graph;
		// various kinds of triplemapping block extraction from the graph
		$this->parse_defaultvalues();
    	$this->parse_logicalSource();
    	$this->parse_subjectMap();
    	$this->parse_subject();
    	
    	// decide the execution order of the triplemapping blocks
    	$this->sort_dependency();

    	// build mapper component for each block
    	return $this->build_mapper();
	}

	private function force_namespace() {
		//forced prefix for used in expansion
		NS::set("rr", "http://www.w3.org/ns/r2rml#");

		NS::set("rml", "http://semweb.mmlab.be/ns/rml#");
		NS::set("ql", "http://semweb.mmlab.be/ns/ql#");
		NS::set("fno", "http://semweb.datasciencelab.be/ns/function#");
		
		NS::set("rmlx", "http://pebbie.org/ns/rmlx#");
		NS::set("qlx", "http://pebbie.org/ns/qlx#");
		NS::set("rop", "http://pebbie.org/ns/rmlx-functions#");
	}

#
# -----------------------------------------------------------------------------
#
	private function parse_logicalSource() {
		#echo NS::expand("rml:logicalSource")."\n";
		foreach($this->graph->resourcesMatching(NS::expand("rml:logicalSource")) as $key => $TripleMap){
    		//echo "rml:logicalSource ".$TripleMap."\n";
	    	$this->mapping[] = $TripleMap;
	        foreach($this->graph->resourcesMatching(NS::expand("rr:parentTriplesMap"), $TripleMap) as $key => $JoinMap)
	        {
	            if($JoinMap != NULL)
	            {
	                $objmap = $this->graph->resourcesMatching(NS::expand("rr:objectMap"), $JoinMap);
	                //print_r($objmap[0]);
	                //break;
	                $ChildMap = $this->graph->resourcesMatching(NS::expand("rr:predicateObjectMap"), $objmap[0]);
	                //$ChildMap = $graph->resourcesMatching(_exp("rr:predicateObjectMap"), $graph->resourcesMatching(_exp("rr:objectMap"), $JoinMap)[0]);

	                $jc = $JoinMap->all("rr:joinCondition");

	                $parentRef = array();
	                foreach($jc as $key => $joincond)
	                {
	                    $parentRef[] = $joincond->get("rr:parent")->getValue();
	                }
	                
	                if(!array_key_exists($TripleMap->getUri(), $this->dependency))
	                {
	                    $this->dependency[$TripleMap->getUri()] = array();
	                }
	                $this->dependency[$TripleMap->getUri()][] = array($parentRef, $ChildMap[0]->getUri());
	            }
	        }
	        $vars = triplemap_vars($TripleMap);
	        foreach($vars as $k => $var)
	            $this->tvars[$var] = $TripleMap;
    	}
	}

	private function parse_subjectMap() {
		//include triplesMap without a logicalSource
	    foreach($this->graph->resourcesMatching(NS::expand("rr:subjectMap")) as $key => $TripleMap)
	    {
    		//echo "rr:subjectMap ".$TripleMap."\n";
	        $tmp = array_search($TripleMap, $this->mapping);
	        if(!$tmp)
	        {
	            $this->mapping[] = $TripleMap;
	        }
	    }
	}

	private function parse_subject() {
		//include triplesMap without a logicalSource
	    foreach($this->graph->resourcesMatching(NS::expand("rr:subject")) as $key => $TripleMap)
	    {
    		//echo "rr:subject ".$TripleMap."\n";
	        $tmp = array_search($TripleMap, $this->mapping);
	        if(!$tmp)
	        {
	            $this->mapping[] = $TripleMap;
	        }
	    }
	}

	private function parse_defaultvalues() {
		//this function returns all variables used in the sourceTemplate (if any)
	    //using path: $tmap rml:logicalSource/rmlx:sourceTemplate $tpl
	    foreach($this->graph->resourcesMatching("rmlx:defaultValue") as $k => $pdef)
	    {
	        foreach($pdef->all("rmlx:defaultValue") as $_k => $pnode)
	        {
	            //var_dump($pnode);
	            $pname = $pnode->get("rmlx:varName")->getValue();
	            $pconst = $pnode->get("rr:constant");
	            $ptemp = $pnode->get("rr:template");
	            $val = $this->default_context->get($pname);
	            if($val != RmlContext::$default_notfound) continue;
	            if($pconst != NULL)
	            {	
	            		$pval = $pconst->getValue();
	            		$this->default_context->put($pname, $pval);
	            }
	            else if($ptemp)
	            {
	                $pval = $this->default_context->apply($ptemp);     
	                $this->default_context->put($pname, $pval);
	            }    
	        }
	        
	    }
	}

	private function parse_transform() {
		//triplesMap with only processing chain (use only global variables)
	    foreach($this->graph->resourcesMatching(NS::expand("rmlx:transform")) as $key => $TripleMap)
	    {
	        $tmp = array_search($TripleMap, $this->mapping);
	        if(!$tmp)
	        {
	            $this->mapping[] = $TripleMap;
	            $vars = triplemap_vars($TripleMap);
	            foreach($vars as $k => $var)
	                $this->tvars[$var] = $TripleMap;
	        }
	    }
	}

	private function sort_dependency() {
		//TODO:sort variable dependency
	    #foreach($tvars as $k=>$v) echo $k." : ".$v->getUri()."\n";

	    foreach($this->mapping as $k => $tmap)
	    {
	        $st = triplemap_depvars($tmap);
	        foreach($st as $kk => $var)
	            if(array_key_exists($var, $this->tvars) && $this->tvars[$var] != $tmap)
	                $this->dependency[$this->tvars[$var]->getUri()][] = array(array(), $tmap->getUri());
	    }
	    
	    $adepth = array();
	    foreach($this->mapping as $k => $map)
	    {
	        $adepth[$map->getUri()] =_depth($map->getUri(), $this->dependency);
	        //TODO: logical source having a data URI should be after a variable is defined
	    }
	    #var_dump($dependency);
	    arsort($adepth);
	    $scount = 0;
	    #var_dump($adepth);
	    //lookup variables stores values requires for join because of rr:parentTripleMap
	    $lookup = array();

	    //TODO: one triple map executed more than once (in case variables used in sourceTemplate is an array)
	    foreach($adepth as $mapuri => $depth)
	    {
	        #echo $mapuri, "\n";
	        if(array_key_exists($mapuri, $this->dependency))
	        {
	            $lookup[$mapuri] = array();
	            foreach($this->dependency[$mapuri] as $kd => $depmap)
	            {
	                $lookup[$mapuri][$depmap[1]] = array();
	            }
	        }

	        $map = $this->graph->resource($mapuri);
	        $this->map_ordering[] = $map;
	        #process_triplemap($map, $output, $initVars, $lookup, $dependency);
	    }
	}

#
# -----------------------------------------------------------------------------
#

	private function build_mapper() {
		$mapper = new Component\RootMapperComponent($this->default_context);

		foreach($this->map_ordering as $key => $map){
    		//echo "processing map ".$map."\n";
    		$tmapper = $this->build_triplemapper($map);
    		if($tmapper != null)
    			$mapper->add_mapper($tmapper);
		}

		return $mapper;
	}

	private function build_triplemapper(&$map) {
		$tmapper = new Component\TripleMapperComponent();

		$ls = $map->get("rml:logicalSource");
    	$lt = $map->get("rr:logicalTable");	
    	$src = null;

    	if($ls) 
    		$src = $this->build_logicalSource($ls);
    	else if($lt)
    		$src = $this->build_logicalTable($lt);

    	if($src != null)
    		$tmapper->set_source($src);

    	$this->build_subjectMapper($map, $tmapper);
    	$this->build_constantTripleMapper($map, $tmapper);
    	$this->build_predicateObjectMapper($map, $tmapper);

    	return $tmapper;
	}

	private function build_logicalSource(&$ls) {
		$src = new Component\SourceMapperComponent();

		$sourceTemplate = $ls->get("rmlx:sourceTemplate");
		if($sourceTemplate){
            $src->add_processor(new Component\SourceTemplateMapper($sourceTemplate->getValue()));
        }
        else
        {
            $sourceName = $ls->get("rml:source");
            if($sourceName == null)
                $sourceName = $ls->get("rml:sourceName");
            $src->add_processor(new Component\SourceMapper($sourceName->getValue()));
        }

        //set source iterator (actual data access component to source)
        $ql = $ls->get("rml:referenceFormulation");
        if($ql == null)
            $ql = $ls->get("rml:queryLanguage");

        $this->build_iterator($ls, $ql);
        
        switch($ql->localName()){
        	case "CSV": $src->set_iterator(new SourceHandler\CSVSource());break;
        	case "JSONPath": $src->set_iterator(new SourceHandler\JSONSource());break;
        	case "XPath": $src->set_iterator(new SourceHandler\XMLSource());break;
        	case "SQL": $src->set_iterator(new SourceHandler\PDOSource());break;

        	default:
        		$src->set_iterator(new SourceHandler\EmptySource());
        }

		
		return $src;
	}

	private function build_iterator($ls, $ql) {
		//parse iterator
        $iterator = $ls->get("rml:iterator");
        if($iterator == null)
            $iterator = $ls->get("rml:separator");
        if($iterator == null && $ql->localName() == 'CSV')
            $iterator = ",";

        //iteratorTemplate?iteratorReference?
        
        if(is_a($iterator, 'EasyRdf_Literal'))
            $iterator = $iterator->getValue();

        $this->default_context->put("__iterator__", $iterator);
	}

	private function build_logicalTable(&$lt) {

		return null;
	}

	private function build_subjectMapper($map, $tmap) {
		$s = $map->get("rr:subject");
    	$smap = $map->get("rr:subjectMap");
    	$subj = null;

    	if($s){
    		if(strlen($s)>2 && strpos($s, "_:")!==false)
    			$subj = new Component\BlankSubjectMapper();
    		else
    			$subj = new Component\IRISubjectMapper($s);
    	} else
    	if($smap){
    		$scls = $smap->get("rr:class");
    		$stpl = $smap->get("rr:template");
    		$scon = $smap->get("rr:constant");
    		$stty = $smap->get("rr:termType");
    		
    		if($stty && $stty==NS::expand("rr:BlankNode"))
    			$subj = new Component\BlankSubjectMapper();
    		else if($scon)
    			$subj = new Component\IRISubjectMapper($scon);
    		else if($stpl)
    			$subj = new Component\IRITemplateSubjectMapper($stpl);

    		if($scls)
    			foreach($smap->all("rr:class") as $cls) {
	    			$po = new Component\PredicateObjectMapperComponent(
	    				new Component\IRIPredicateMapper(NS::expand("rdf:type")),
	    				new Component\IRIObjectMapper($cls)
	    			);
	    			$tmap->add_predicateobject($po);
    			}
   
    	}
    	$tmap->set_subject($subj);
	}

	private function build_constantTripleMapper($map, $tmap) {
		$pred = $map->get("rr:predicate");
    	$ipred = $map->get("rmlx:predicateInv");
    	$_obj = $map->get("rr:object");

    	if($_obj){
    		$pmap = $this->get_predicateMapper($map);
    		if($ipred)
    			$po = new Component\InversePredicateObjectMapperComponent($pmap);
    		else
    			$po = new Component\PredicateObjectMapperComponent($pmap);

    		$objs = $map->all("rr:object");
    		foreach($objs as $obj)
    			$po->add_object_mapper(new Component\IRIObjectMapper($obj));

    		$tmap->add_predicateobject($po);
    	}
	}

	private function get_predicateMapper($map) {
		$pred = $map->get("rr:predicate");
    	$ipred = $map->get("rmlx:predicateInv");
    	$pmap = $map->get("rr:predicateMap");
    	if($pred)
    		return new Component\IRIPredicateMapper($pred);
    	else if($ipred)
    		return new Component\IRIPredicateMapper($ipred);
    	else if($pmap) {
    		$ptpl = $pmap->get("rr:template");
    		$pcon = $pmap->get("rr:constant");
    		if($pcon)
    			return new Component\IRIPredicateMapper($pcon);
    		else
    			return new Component\IRITemplatePredicateMapper($ptpl);
    	}
	}

	private function build_predicateObjectMapper($map, $tmap) {
		foreach($map->all("rr:predicateObjectMap") as $pomap){
			$pred = $pomap->get("rr:predicate");
			$ipred = $pomap->get("rmlx:predicateInv");
    		$pmap = $this->get_predicateMapper($pomap);

			if($ipred)
    			$po = new Component\InversePredicateObjectMapperComponent($pmap);			
    		else
    			$po = new Component\PredicateObjectMapperComponent($pmap);

    		$_obj = $pomap->get("rr:object");
    		$omap = $pomap->get("rr:objectMap");
    		if($_obj){
				$objs = $pomap->all("rr:object");
	    		foreach($objs as $obj){
	    			$po->add_object_mapper(new Component\IRIObjectMapper($obj));    			
	    		}
    		}
    		if($omap){
    			$this->build_objectMapper($omap, $po);
    		}


    		$tmap->add_predicateobject($po);
		}	
	}

	private function build_objectMapper($omap, &$po) {
		$ttyp = $omap->get("rr:termType");
		$otpl = $omap->get("rr:template");
		$ocon = $omap->get("rr:constant");
		$oref = $omap->get("rml:reference");
		$ocol = $omap->get("rr:column");
		$opar = $omap->get("rr:parentTriplesMap");

		if($ttyp == NS::expand("rr:IRI")){
			if($ocon)
				$po->add_object_mapper(new Component\IRIObjectMapper($ocon));
			else if($otpl)
				$po->add_object_mapper(new Component\IRITemplateObjectMapper($otpl));
		}
		else if($ttyp == NS::expand("rr:BlankNode")){
			if($otpl)
				$po->add_object_mapper(new Component\BlankTemplateObjectMapper($otpl));
			else if($ocon)
				$po->add_object_mapper(new Component\BlankObjectMapper($ocon));	
			else
				$po->add_object_mapper(new Component\BlankObjectMapper());
		}
		else {
			//Literals
			$lang = $omap->get("rr:language");
			$dtyp = $omap->get("rr:datatype");

			if($ocon)
				$omapper = new Component\ConstantLiteralMapper($ocon);
			else if($otpl)
				$omapper = new Component\TemplateLiteralMapper($otpl);
			else if($oref)
				$omapper = new Component\ReferenceLiteralMapper($oref);

			if($lang != null)
				$omapper->set_language($lang);
			if($dtyp != null)
				$omapper->set_datatype($dtyp);

			$po->add_object_mapper($omapper);
			
		}
	}

}

#
# -----------------------------------------------------------------------------
#

function triplemap_vars($tmap)
{
    //this function returns all variables generated in this triplemap
    //using path: $tmap rmlx:transform/rmlx:outputVar $varname
    $result = array();
    $xform = $tmap->all("rmlx:transform");
    foreach($xform as $k => $varnode)
    {
        $result[] = $varnode->get("rmlx:outputVar")->getValue();
    }
    return $result;
}

function triplemap_depvars($tmap)
{
    //this function returns all dependent variables used in every occurence of rr:template or rml:reference
    //using path: $tmap rml:logicalSource/rmlx:sourceTemplate $tpl (sourceTemplate)
    //$tmap rml:subjectMap/rr:template $tpl
    //$tmap rml:subjectMap/rml:reference $ref
    //$tmap rml:predicateObjectMap/rml:predicateMap/rr:template $tpl
    //$tmap rml:predicateObjectMap/rml:predicateMap/rml:reference $ref
    //$tmap rml:predicateObjectMap/rml:objectMap/rr:template $tpl
    //$tmap rml:predicateObjectMap/rml:objectMap/rml:reference $ref
    $result = array();
    $ls = $tmap->get("rml:logicalSource");
    if($ls)
    {
        $tpl = $ls->get("rmlx:sourceTemplate");
        if($tpl)
        {
            $result = array_merge($result, extract_vars($tpl->getValue()));
        }
    }
    
    $tm = $tmap->get("rr:subjectMap");
    if($tm)
        $result = array_merge($result, termmap_inputvars($tm));
    foreach($tmap->all("rr:predicateObjectMap") as $k => $pomap)
    {
        $tm = $tmap->get("rr:predicateMap");
        if($tm)
            $result = array_merge($result, termmap_inputvars($tm));
        $tm = $tmap->get("rr:objectMap");
        if($tm)
            $result = array_merge($result, termmap_inputvars($tm));
    }
    return array_unique($result);
}

function termmap_inputvars($tm)
{
    $result = array();
    $tpl = $tm->get("rr:template");
    $tr = $tm->get("rml:reference");
    if($tpl)
    {
        $result = array_merge($result, extract_vars($tpl));
    }
    else if($tr)
    {
        $result[] = $tr->getValue();
    }
    return $result;
}

function _depth($node, $dep)
{
    if(!array_key_exists($node, $dep)) return 0;
    $branch = array();
    foreach($dep[$node] as $key => $value){
        $branch[] = _depth($value[1], $dep);
    }
    return 1+max($branch);
}

function _vdepth($vname, $vdep)
{
    if(!array_key_exists($vname, $vdep)) return 0;
    $branch = 0;
    foreach($vdep[$vname] as $key => $value){
        $branch += _vdepth($value, $vdep);
    }
    return 1+$branch;
}

?>