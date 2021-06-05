<?php
	function genRandomUUIDStr()
	{
		$uuidres = $this->uuid->generate(UUID::UUID_RANDOM);
		$result['uuid'] = $this->uuid->conv_byte2string($uuidres);
	}
?>