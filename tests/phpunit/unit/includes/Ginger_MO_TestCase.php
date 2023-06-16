<?php

class Ginger_MO_TestCase extends \PHPUnit\Framework\TestCase {

	protected $temp_files = array();

	// Create temporary files
	protected function temp_file( $contents = null ) {
		$file = tempnam( sys_get_temp_dir(), 'gingermo' );
		file_put_contents( $file, $contents );
		$this->temp_files[] = $file;
		return $file;
	}

	public function __destruct() {
		foreach ( $this->temp_files as $file ) {
			unlink( $file );
		}
		$this->temp_files = array();
	}
}