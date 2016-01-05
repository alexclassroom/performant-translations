<?php

class Ginger_MO {
	protected $default_textdomain = 'default';
	protected $loaded_translations = array(); // [ Textdomain => [ .., .. ] ]

	protected $fallback_to_default_textdomain = false;

	public static function instance() {
		static $instance = false;
		return $instance ? $instance : $instance = new Ginger_MO();
	}

	public function load( $translation_file, $textdomain = null ) {
		$moe = Ginger_MO_Translation_File::create( $translation_file );
		if ( ! $moe ) {
			return false;
		}

		if ( ! $textdomain ) {
			$textdomain = $this->default_textdomain;
		}

		if ( ! isset( $this->loaded_translations[ $textdomain ] ) ) {
			$this->loaded_translations[ $textdomain ] = array();
		}

		// Prefix translations to ensure that last-loaded takes preference
		array_unshift( $this->loaded_translations[ $textdomain ], $moe );

		return true;
	}

	public function fallback_to_default_textdomain( $set = null ) {
		if ( null !== $set ) {
			$this->fallback_to_default_textdomain = $set;
		}
		return $this->fallback_to_default_textdomain;
	}

	public function unload( $textdomain, $mo = null ) {	
		if ( ! $this->is_loaded( $textdomain ) ) {
			return false;
		}
		if ( $mo ) {
			foreach ( $this->loaded_translations[ $textdomain ] as $i => $moe ) {
				if ( $mo === $moe ) {
					unset( $this->loaded_translations[ $textdomain ][ $i ] );
					return true;
				}
			}
			return true;
		}

		unset( $this->loaded_translations[ $textdomain ] );
		return true;
	}

	public function is_loaded( $textdomain ) {
		return !empty( $this->loaded_translations[ $textdomain ] );
	}

	public function translate( $text, $context, $textdomain = null ) {
		if ( $context ) {
			$context .= "\4";
		}

		$translation = $this->locate_translation( "{$context}{$text}", $textdomain );
		return $translation ? $translation['entries'] : $text;
	}

	public function translate_plural( $plurals, $number, $context, $textdomain = null ) {
		if ( $context ) {
			$context .= "\4";
		}
		$text = implode( "\0", $plurals );
		$translation = $this->locate_translation( "{$context}{$text}", $textdomain );

		if ( $translation ) {
			$t = is_array( $translation['entries'] ) ? $translation['entries'] : explode( "\0", $translation['entries'] );
			$num = $translation['source']->get_plural_form( $number );
		} else {
			$t = $plurals;
			// Fallback to english grammer
			$num = ( $number == 1 ? 0 : 1 );
		}

		return $t[ $num ];
	}

	protected function locate_translation( $string, $textdomain = null ) {
		if ( ! $this->loaded_translations ) {
			return false;
		}
		if ( ! $textdomain ) {
			$textdomain = $this->default_textdomain;
		}

		// Find the translation in all loaded files for this text domain
		foreach ( $this->get_mo_files( $textdomain ) as $moe ) {
			if ( false !== ( $translation = $moe->translate( $string ) ) ) {
				return array(
					'entries' => $translation,
					'source' => $moe
				);
			}
			if ( $moe->error() ) {
				// Unload this file, something is wrong.
				$this->unload( $textdomain, $moe );
			}
		}

		// Nothing could be found
		return false;
	}

	protected function get_mo_files( $textdomain = null ) {
		$moes = array();
		if ( isset( $this->loaded_translations[ $textdomain ] ) ) {
			$moes = $this->loaded_translations[ $textdomain ];
		}

		if ( $this->fallback_to_default_textdomain && $textdomain != $this->default_textdomain ) {
			$moes = array_merge( $moes, $this->get_mo_files( $this->default_textdomain ) );
		}

		return $moes;
	}
}
