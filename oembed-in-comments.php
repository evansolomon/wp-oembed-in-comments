<?php

/*
Plugin Name: oEmbed in Comments
Description: Allow oEmbeds in comment text
Version: 1.1
Author: Evan Solomon
Author URI: http://evansolomon.me
*/

class ES_oEmbed_Comments {
	/**
	 * Add generic actions
	 */
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * If we're not in the admin, keep going
	 */
	function init() {
		if ( is_admin() )
			return;

		$this->oembed_in_comments();
	}

	/**
	 * Setup filter to do oEmbed in comments
	 */
	function oembed_in_comments() {
		// make_clickable breaks oEmbed regex, make sure we go earlier
		$clickable = has_filter( 'comment_text', 'make_clickable' );
		$priority = ( $clickable ) ? $clickable - 1 : 10;

		add_filter( 'comment_text', array( $this, 'oembed_filter' ), $priority );
	}

	/**
	 * Wrap WP_Embed::autoembed() and make sure auto-discovery is off
	 */
	function oembed_filter( $comment_text ) {
		global $wp_embed;

		add_filter( 'embed_oembed_discover', '__return_false', 999 );
		$comment_text = $wp_embed->autoembed( $comment_text );
		remove_filter( 'embed_oembed_discover', '__return_false', 999 );

		return $comment_text;
	}
}

new ES_oEmbed_Comments;
