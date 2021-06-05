<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	function genRandomUUIDStr()
	{
		$uuidres = $this->uuid->generate(UUID::UUID_RANDOM);
		$uuid = $this->uuid->conv_byte2string($uuidres);
		return $uuid;
	}
?>