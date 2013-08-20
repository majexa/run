<?php

class LogNotify {
	
	static public function send($file) {
		if (!file_exists(LogReader::getPath($name))) return false;
		$text = getPrr(LogReader::get($name));
		//mail('masted311@gmail.com', );
	}
	
}