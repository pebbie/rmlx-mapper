<?php

namespace Rmlx\Component;

use Rmlx\SourceHandler\EmptySource;

class TripleMapperComponent extends RootMapperComponent {
	// A TripleMapper contains 

	// context processors
	private $ctx_processors;

	// data source
	private $source_mapper;

	// subject mapper
	private $subject_mapper;

	// a set of predicate-object mapper that will be executed based on 
	// how many data was taken from source
	private $po_mapper;

	function __construct($src=null, $subj=null){
		$this->ctx_processors = array();
		$this->source_mapper = $src;
		$this->subject_mapper = $subj;
		$this->po_mapper = array();
	}

	public function set_source($src){
		$this->source_mapper = $src;
	}

	public function set_subject($subj){
		$this->subject_mapper = $subj;
	}

	public function add_predicateobject($po){
		$this->po_mapper[] = $po;
	}

	public function add_processor($proc){
		$this->ctx_processors[] = $proc;
	}

	public function map(&$context) {
		#echo "executing triplemapper\n";
		if($this->source_mapper == null)
			$this->source_mapper = new SourceMapperComponent(new EmptySource());
		
		//setup
		$this->source_mapper->map($context);
		$source_iterator = $this->source_mapper->get_iterator();
		
		//guard
		if($this->subject_mapper == null) return;

		foreach($source_iterator->iterate($context->get("__iterator__")) as $ctx){
			$ctx->push_context($context);
			foreach($this->ctx_processors as $proc){
				//processor will update values
				$proc->process($ctx);
			}
			$this->subject_mapper->map($ctx);
			foreach($this->po_mapper as $po){
				$po->map($ctx);
			}
		}
	}
}

?>