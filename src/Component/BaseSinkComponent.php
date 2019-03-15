<?php

// interface for storing the emitted triple

namespace Rmlx\Component;

interface BaseSinkComponent {
	public function open($context);
	public function add($subject, $predicate, $object, $graph=null);
	public function close($context);
}

?>