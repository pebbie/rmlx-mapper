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

	// root component of the mapper tree
	private $root;

	// context for reference lookup
	private $context;

	// graph for storing RML mapping description
	private $rml_graph;

	// filename for storing output RDF
	private $out_filename;
	private $out_format;

	function __construct($initVars=array()) {
		$this->context = new RmlContext($initVars);
		$this->context->put("__mapper__", $this);
	}

	function open($location, $format="turtle") {
		//check if format requires preprocessing (e.g. YARML) before loading as RDF Graph
		$this->rml_graph = new EasyRdf_Graph();
		$this->rml_graph->parse(file_get_contents($location), $format);
	}

	function setOutput($location, $format="ntriples") {
		// just need to store this
		// depends on the strategy, can be both streaming or buffering
		$this->out_filename = $location;
		$this->out_format = $format;
	}

	function run() {
		echo "parsing RML mapping description\n";
		$parser = new RmlParser();
		$this->root = $parser->parse($this->rml_graph);
		if($this->root != null){
			echo "execute RML mapping\n";
			return $this->root->map($this->context);
		}
		return null;
	}
}

?>