<?php

/*
 * Mapper Class that can be used directly by user
 *
 * Mapping works by building a tree composition of MapperComponent where each 
 * component achored to the RDF Term in the RML Mapping Graph Document
 * and passes down information to the child components
 * 
 * MappingJobComponent
 * - MappingSourceComponent : 
 * 	 - ConstantURL
 *	 - TemplatedURL
 *	 - DerivedSource (derived from other source)
 * - TripleMappingComponent
 *   - SubjectMappingComponent : ConstantIRI, BlankNode, Template
 *   	- PredicateObjectMappingComponent : 
 			Forward, 
 			Backward
 *			- PredicateMappingComponent : 
 				ConstantIRI, 
 				Template
 *			- ObjectMappingComponent : 
 				ConstantLiteral, 
 				ConstantIRI, 
 				ReferenceIRI, 
 				ReferenceLiteral, 
 				TemplateIRI, 
 				TemplateLiteral
 * 
 */
namespace Rmlx;

use EasyRdf_Graph;

class RmlMapper {

	// context for reference lookup
	private $context;

	// filename for storing output RDF
	private $out_filename;
	private $out_format;

	private $sink;

	function __construct($initVars=array()) {
		$this->context = new RmlContext($initVars);

		$this->context->put("__mapper__", $this);

		// sink are receivers of triples
		$sink = array();
	}

	function load($location, $format="turtle") {
		//check if format requires preprocessing (e.g. YARML) before loading as RDF Graph
		$src_dir = $this->context->get("src_dir");

		$rml_graph = new EasyRdf_Graph();

		if(file_exists($location)) {
			$rml_graph->parse(file_get_contents($location), $format);			
		} else if($src_dir != null && file_exists($src_dir.$location)) {
			$rml_graph->parse(file_get_contents($src_dir.$location), $format);
		}
		
		$this->context->put("__mapgraph__", $rml_graph);
	}

	function set_output($location, $format="ntriples") {
		// just need to store this
		// depends on the strategy, can be both streaming or buffering
		$this->out_filename = $location;
		$this->out_format = $format;
		$this->add_sink(new Component\GraphSinkComponent($location, $format));
	}

	function add_sink($sink){
		$this->sink[] = $sink;
	}

	function run() {
		echo "parsing RML mapping description\n";
		$parser = new RmlParser();
		$graph = $this->context->get("__mapgraph__");
		$root = $parser->parse($graph);

		$this->open($this->context);

		//do the actual mapping (generate triples)
		if($root != null){
			echo "execute RML mapping\n";
			$result = $root->map($this->context);
		}
		
		$this->close($this->context);
		return $this->context->get("__provenance__");
		var_dump($this->context);
	}

	//BaseSinkComponent implementation
	function open($context) {
		//open each sink (create a file or open connection)
		foreach($this->sink as $sink)
			$sink->open($context);
	}

	function add($subject, $predicate, $object, $graph=null){
		foreach($this->sink as $sink)
			$sink->add($subject, $predicate, $object, $graph);
	}

	function close($context) {
		//close each sink (close file or close connection)
		foreach($this->sink as $sink)
			$sink->close($context);
	}
}

?>