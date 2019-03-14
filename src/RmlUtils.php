<?php

namespace Rmlx;

use EasyRdf_Namespace;

class RmlUtils {
	public static function _exp($s)
	{
	    return EasyRdf_Namespace::expand($s);
	}
}


?>