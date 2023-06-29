<?php

/**
 * @coversDefaultClass Ginger_MO_Translation_Compat_Provider
 */
class Ginger_MO_Translation_Compat_Provider_Tests extends WP_UnitTestCase {
	/**
	 * @return void
	 */
	public function tear_down() {
		if ( file_exists( DIR_TESTDATA . '/pomo/simple.php' ) ) {
			$this->unlink( DIR_TESTDATA . '/pomo/simple.php' );
		}

		if ( file_exists( DIR_TESTDATA . '/pomo/plural.php' ) ) {
			$this->unlink( DIR_TESTDATA . '/pomo/plural.php' );
		}
	}

	/**
	 * @covers ::__get
	 * @covers ::make_entry
	 *
	 * @return void
	 */
	public function test_get_entries() {
		global $l10n;

		load_textdomain( 'wp-tests-domain', DIR_TESTDATA . '/pomo/simple.mo' );

		$compat_instance = isset( $l10n['wp-tests-domain'] ) ? $l10n['wp-tests-domain'] : null;

		$entries = $compat_instance ? $compat_instance->entries : array();

		$unload_successful = unload_textdomain( 'wp-tests-domain' );

		$this->assertInstanceOf( Ginger_MO_Translation_Compat_Provider::class, $compat_instance, 'No compat provider instance used' );
		$this->assertTrue( $unload_successful, 'Text domain not successfully unloaded' );
		$this->assertEqualSets(
			array(
				new Translation_Entry(
					array(
						'singular'     => 'baba',
						'translations' => array( 'dyado' ),
					)
				),
				new Translation_Entry(
					array(
						'singular'     => "kuku\nruku",
						'translations' => array( 'yes' ),
					)
				),
			),
			$entries,
			'Actual translation entries do not match expected ones'
		);
	}

	/**
	 * @covers ::__get
	 * @covers ::make_entry
	 *
	 * @return void
	 */
	public function test_get_entries_plural() {
		global $l10n;

		load_textdomain( 'wp-tests-domain', DIR_TESTDATA . '/pomo/plural.mo' );

		$compat_instance = isset( $l10n['wp-tests-domain'] ) ? $l10n['wp-tests-domain'] : null;

		$entries = $compat_instance ? $compat_instance->entries : array();

		$unload_successful = unload_textdomain( 'wp-tests-domain' );

		$this->assertInstanceOf( Ginger_MO_Translation_Compat_Provider::class, $compat_instance, 'No compat provider instance used' );
		$this->assertTrue( $unload_successful, 'Text domain not successfully unloaded' );
		$this->assertEqualSets(
			array(
				new Translation_Entry(
					array(
						'singular'     => 'one dragon',
						'plural'       => '%d dragons',
						'translations' => array(
							'oney dragoney',
							'twoey dragoney',
							'manyey dragoney',
							'manyeyey dragoney',
							'manyeyeyey dragoney',
						),
					)
				),
			),
			$entries,
			'Actual translation entries do not match expected ones'
		);
	}


	/**
	 * @covers ::__get
	 * @covers ::make_entry
	 *
	 * @return void
	 */
	public function test_get_entries_context() {
		global $l10n;

		load_textdomain( 'wp-tests-domain', DIR_TESTDATA . '/pomo/context.mo' );

		$compat_instance = isset( $l10n['wp-tests-domain'] ) ? $l10n['wp-tests-domain'] : null;

		$entries = $compat_instance ? $compat_instance->entries : array();

		$unload_successful = unload_textdomain( 'wp-tests-domain' );

		$this->assertInstanceOf( Ginger_MO_Translation_Compat_Provider::class, $compat_instance, 'No compat provider instance used' );
		$this->assertTrue( $unload_successful, 'Text domain not successfully unloaded' );
		$this->assertEqualSets(
			array(
				new Translation_Entry(
					array(
						'context'      => 'not so dragon',
						'singular'     => 'one dragon',
						'translations' => array( 'oney dragoney' ),
					)
				),
				new Translation_Entry(
					array(
						'is_plural'    => true,
						'singular'     => 'one dragon',
						'plural'       => '%d dragons',
						'context'      => 'dragonland',
						'translations' => array(
							'oney dragoney',
							'twoey dragoney',
							'manyey dragoney',
						),
					)
				),
			),
			$entries,
			'Actual translation entries do not match expected ones'
		);
	}


	/**
	 * @covers ::__get
	 *
	 * @return void
	 */
	public function test_get_headers() {
		global $l10n;

		load_textdomain( 'wp-tests-domain', DIR_TESTDATA . '/pomo/simple.mo' );

		$compat_instance = isset( $l10n['wp-tests-domain'] ) ? $l10n['wp-tests-domain'] : null;

		$headers = $compat_instance ? $compat_instance->headers : array();

		$unload_successful = unload_textdomain( 'wp-tests-domain' );

		$this->assertInstanceOf( Ginger_MO_Translation_Compat_Provider::class, $compat_instance, 'No compat provider instance used' );
		$this->assertTrue( $unload_successful, 'Text domain not successfully unloaded' );
		$this->assertEqualSetsWithIndex(
			array(
				'Project-Id-Version'   => 'WordPress 2.6-bleeding',
				'Report-Msgid-Bugs-To' => 'wp-polyglots@lists.automattic.com',
			),
			$headers,
			'Actual translation headers do not match expected ones'
		);
	}

	/**
	 * @covers ::translate
	 *
	 * @return void
	 */
	public function test_translate() {
		global $l10n;

		load_textdomain( 'wp-tests-domain', DIR_TESTDATA . '/pomo/simple.mo' );

		$compat_instance = isset( $l10n['wp-tests-domain'] ) ? $l10n['wp-tests-domain'] : null;

		$translation         = $compat_instance ? $compat_instance->translate( 'baba' ) : false;
		$translation_missing = $compat_instance ? $compat_instance->translate( 'does not exist' ) : false;

		$unload_successful = unload_textdomain( 'wp-tests-domain' );

		$this->assertInstanceOf( Ginger_MO_Translation_Compat_Provider::class, $compat_instance, 'No compat provider instance used' );
		$this->assertSame( 'dyado', $translation, 'Actual translation does not match expected one' );
		$this->assertSame( 'does not exist', $translation_missing, 'Actual translation fallback does not match expected one' );
		$this->assertTrue( $unload_successful, 'Text domain not successfully unloaded' );
	}

	/**
	 * @covers ::translate_plural
	 *
	 * @return void
	 */
	public function test_translate_plural() {
		global $l10n;

		load_textdomain( 'wp-tests-domain', DIR_TESTDATA . '/pomo/plural.mo' );

		$compat_instance = isset( $l10n['wp-tests-domain'] ) ? $l10n['wp-tests-domain'] : null;

		$translation_1       = $compat_instance ? $compat_instance->translate_plural( 'one dragon', '%d dragons', 1 ) : false;
		$translation_2       = $compat_instance ? $compat_instance->translate_plural( 'one dragon', '%d dragons', 2 ) : false;
		$translation_minus_8 = $compat_instance ? $compat_instance->translate_plural( 'one dragon', '%d dragons', -8 ) : false;

		$unload_successful = unload_textdomain( 'wp-tests-domain' );

		$this->assertInstanceOf( Ginger_MO_Translation_Compat_Provider::class, $compat_instance, 'No compat provider instance used' );
		$this->assertSame( 'oney dragoney', $translation_1, 'Actual translation does not match expected one' );
		$this->assertSame( 'twoey dragoney', $translation_2, 'Actual translation does not match expected one' );
		$this->assertSame( 'twoey dragoney', $translation_minus_8, 'Actual translation does not match expected one' );
		$this->assertTrue( $unload_successful, 'Text domain not successfully unloaded' );
	}

	/**
	 * @covers ::translate_plural
	 *
	 * @return void
	 */
	public function test_translate_plural_missing() {
		global $l10n;

		load_textdomain( 'wp-tests-domain', DIR_TESTDATA . '/pomo/plural.mo' );

		$compat_instance = isset( $l10n['wp-tests-domain'] ) ? $l10n['wp-tests-domain'] : null;

		$translation_1 = $compat_instance ? $compat_instance->translate_plural( '%d house', '%d houses', 1 ) : false;
		$translation_2 = $compat_instance ? $compat_instance->translate_plural( '%d car', '%d cars', 2 ) : false;

		$unload_successful = unload_textdomain( 'wp-tests-domain' );

		$this->assertInstanceOf( Ginger_MO_Translation_Compat_Provider::class, $compat_instance, 'No compat provider instance used' );
		$this->assertSame( '%d house', $translation_1, 'Actual translation fallback does not match expected one' );
		$this->assertSame( '%d cars', $translation_2, 'Actual plural translation fallback does not match expected one' );
		$this->assertTrue( $unload_successful, 'Text domain not successfully unloaded' );
	}
}