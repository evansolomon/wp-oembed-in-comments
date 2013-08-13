<?php

/*
Plugin Name: oEmbed in Comments
Description: Allow oEmbeds in comment text
Version: 1.1.2
Author: Evan Solomon
Author URI: http://evansolomon.me
*/

class ES_oEmbed_Comments {

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		if ( is_admin() )
			return;

		$this->add_filter();
	}

	/**
	 * Setup filter with correct priority to do oEmbed in comments
	 */
	function add_filter() {
		// make_clickable breaks oEmbed regex, make sure we go earlier
		$clickable = has_filter( 'get_comment_text', 'make_clickable' );
		$priority = ( $clickable ) ? $clickable - 1 : 10;

		add_filter( 'get_comment_text', array( $this, 'oembed_filter' ), $priority );
	}

	/**
	 * Safely add oEmbed media to a comment
	 */
	function oembed_filter( $comment_text ) {
		global $wp_embed;

		// Automatic discovery would be a security risk, safety first
		add_filter( 'embed_oembed_discover', '__return_false', 999 );
		$comment_text = $wp_embed->autoembed( $comment_text );

		// ...but don't break your posts if you use it
		remove_filter( 'embed_oembed_discover', '__return_false', 999 );

		return $comment_text;
	}
}

new ES_oEmbed_Comments;
