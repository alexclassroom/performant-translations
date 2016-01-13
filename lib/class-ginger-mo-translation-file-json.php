<?php

class Ginger_MO_Translation_File_JSON extends Ginger_MO_Translation_File {
	protected function parse_file() {
		$data = file_get_contents( $this->file );
		if ( '/' == $data[0] ) {
			$data = substr( $data, strpos( $data, '{' ) );
		}
		$data = json_decode( $data, true );

		if ( ! $data || ! is_array( $data ) ) {
			$this->error = true;
			return;
		}

		// Support JED JSON files which wrap po2json
		if ( isset( $data['domain'] ) && isset( $data['locale_data'][ $data['domain'] ] ) ) {
			$data = $data['locale_data'][ $data['domain'] ];
		}

		if ( isset( $data[''] ) ) {
			$this->headers = array_change_key_case( $data[''], CASE_LOWER );
			unset( $data[''] );
		}

		foreach ( $data as $key => $item ) {
			if ( ! is_array( $item ) ) {
				// Straight Key => Value translations
				$this->entries[ $key ] = $item;
			} else {
				// po2json format
				if ( null !== $item[0] ) {
					// Plurals
					$key .= "\0" . $item[0];
					$this->entries[ $key ] = array_slice( $item, 1 );
				} else {
					// Singular
					$this->entries[ $key ] = $item[1];
				}
			}
		}

		$this->parsed = true;
	}

	protected function create_file( $headers, $entries ) {
		// json headers are lowercase
		$headers = array_change_key_case( $headers );
		// Prefix as the first key.
		$entries = array_merge( array( '' => $headers ), $entries );

		$json_flags = 0;
		if ( defined( 'JSON_PRETTY_PRINT' ) ) {
			$json_flags |= JSON_PRETTY_PRINT;
		}

		return (bool) file_put_contents( $this->file, json_encode( (array) $entries, $json_flags ) );
	}
}

