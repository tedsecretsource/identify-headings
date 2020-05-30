<?php
/**
 * Class SampleTest
 *
 * @package Identify_Headings
 */

/**
 * Sample test case.
 */
class FindElementsTest extends WP_UnitTestCase {
	/**
	 * The ID of the test post author
	 *
	 * @var integer The ID of the post author
	 */
	public $author_id = 0;

	public $perfectly_formed_no_ids_id = null;
	public $perfectly_formed_existing_ids_id = null;

	const SOURCE_FILES = '/sample-input';

	/**
	 * Create an author for our posts
	 * Create some posts I can use as examples
	 */
	public function setUp() {
		parent::setUp();

		$this->author_id = self::factory()->user->create(
			[ 'role' => 'editor' ]
		);

		$this->perfectly_formed_no_ids_id = self::factory()->post->create(
			[
				'title'        => 'Perfectly Formed, No IDs',
				'post_author'  => $this->author_id,
				'post_content' => file_get_contents( __DIR__ . self::SOURCE_FILES . DIRECTORY_SEPARATOR . 'Perfectly-Formed-No-IDs.html' ),
			]
		);

		$this->perfectly_formed_existing_ids_id = self::factory()->post->create(
			[
				'title'        => 'Perfectly Formed, Existing IDs',
				'post_author'  => $this->author_id,
				'post_content' => file_get_contents( __DIR__ . self::SOURCE_FILES . DIRECTORY_SEPARATOR . 'Perfectly-Formed-Existing-IDs.html' ),
			]
		);

		$this->irregularly_formed_no_ids_id = self::factory()->post->create(
			[
				'title'        => 'Irregularly Formed, No IDs',
				'post_author'  => $this->author_id,
				'post_content' => file_get_contents( __DIR__ . self::SOURCE_FILES . DIRECTORY_SEPARATOR . 'Irregular-Layout-No-IDs.html' ),
			]
		);

		$this->irregularly_formed_existing_ids_id = self::factory()->post->create(
			[
				'title'        => 'Irregularly Formed, Existing IDs',
				'post_author'  => $this->author_id,
				'post_content' => file_get_contents( __DIR__ . self::SOURCE_FILES . DIRECTORY_SEPARATOR . 'Irregular-Layout-Existing-IDs.html' ),
			]
		);

		$this->empty_post_id = self::factory()->post->create(
			[
				'title'        => 'Irregularly Formed, Existing IDs',
				'post_author'  => $this->author_id,
				'post_content' => file_get_contents( __DIR__ . self::SOURCE_FILES . DIRECTORY_SEPARATOR . 'Perfectly-Formed-No-Elements.html' ),
			]
		);
	}

	/**
	 * Test headings are as expected
	 */
	public function test_headings() {
		$post    = get_post( $this->perfectly_formed_no_ids_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertContains( '<h1 id="id-test-input-h1-1" class="ih-linkifier"', $content );
		$this->assertContains( '<h2 id="id-test-input-h2-2" class="ih-linkifier"', $content );
		$this->assertContains( '<h3 id="id-test-input-h3-3" class="ih-linkifier"', $content );
		$this->assertContains( '<h4 id="id-test-input-h4-4" class="ih-linkifier"', $content );
		$this->assertContains( '<h5 id="id-test-input-h5-5" class="ih-linkifier"', $content );
		$this->assertContains( '<h6 id="id-test-input-h6-6" class="ih-linkifier"', $content );

		$post    = get_post( $this->perfectly_formed_existing_ids_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertContains( '<h1 id="a" class="ih-linkifier"', $content );
		$this->assertContains( '<h2 id="b" class="ih-linkifier"', $content );
		$this->assertContains( '<h3 id="c" class="ih-linkifier"', $content );
		$this->assertContains( '<h4 id="d" class="ih-linkifier"', $content );
		$this->assertContains( '<h5 id="e" class="ih-linkifier"', $content );
		$this->assertContains( '<h6 id="f" class="ih-linkifier"', $content );

		$post    = get_post( $this->irregularly_formed_no_ids_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertContains( '<h1 id="id-testinput-h1-1" class="ih-linkifier"', $content );
		$this->assertContains( '<h2 id="id-test-input-h2-2" class="whatever ih-linkifier"', $content );
		$this->assertContains( '<h3 id="id-test-input-lskdjfls-sdfsdlj-3" class="something else tonight ih-linkifier"', $content );
		$this->assertContains( '<h4 id="id-test-input-h4-4" class="ih-linkifier"', $content );
		$this->assertContains( '<h5 id="id-test-input-h5-5" class="ih-linkifier"', $content );
		$this->assertContains( '<h6 id="id-test-input-h6-6" class="ih-linkifier"', $content );

		$post    = get_post( $this->irregularly_formed_existing_ids_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertContains( '<h1 id="a" class="ih-linkifier"', $content );
		$this->assertContains( '<h2 id="b" class="ih-linkifier"', $content );
		$this->assertContains( '<h3 id="c" class="ih-linkifier"', $content );
		$this->assertContains( '<h4 style="font-family: Helvetica, \'Good Ole Helvetica\', sans-serif" id="d" data-lurid="ha ha ha" class="ih-linkifier"', $content );
		$this->assertContains( '<h5 id="e" class="ih-linkifier"', $content );
		$this->assertContains( '<h6 id="f" class="ih-linkifier"', $content );

		$post    = get_post( $this->empty_post_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertTrue( '' === $content );
	}

	/**
	 * Test paragraphs are as expected
	 */
	public function test_paragraphs() {
		$post    = get_post( $this->perfectly_formed_no_ids_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertContains( '<p id="', $content );

		$post    = get_post( $this->perfectly_formed_existing_ids_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertContains( '<p id="g"', $content );

		$post    = get_post( $this->irregularly_formed_no_ids_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertContains( '<p id="', $content );

		$post    = get_post( $this->irregularly_formed_existing_ids_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertContains( '<p data-id="g" id="g" class="something else ih-linkifier"', $content );
	}

	/**
	 * Test lists are as expected
	 */
	public function test_lists() {
		$post    = get_post( $this->perfectly_formed_no_ids_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertContains( '<ul id="', $content );
		$this->assertContains( '<ol id="', $content );

		$post    = get_post( $this->perfectly_formed_existing_ids_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertContains( '<ul id="h"', $content );
		$this->assertContains( '<ol id="i"', $content );

		$post    = get_post( $this->irregularly_formed_no_ids_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertContains( '<ul id="', $content );
		$this->assertContains( '<ol id="', $content );

		$post    = get_post( $this->irregularly_formed_existing_ids_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertContains( '<ul id="h"', $content );
		$this->assertContains( '<ol id="i"', $content );
	}

	/**
	 * Generic test to see if our class is appended to exisitng classes
	 */
	public function test_classes() {
		$post    = get_post( $this->irregularly_formed_existing_ids_id );
		$content = apply_filters( 'the_content', $post->post_content );
		$this->assertContains( 'class="something else ih-linkifier"', $content );
	}
}
