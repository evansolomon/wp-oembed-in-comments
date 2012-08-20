<?php

/*
Plugin Name: oEmbed in Comments
Description: Allow oEmbeds in comment text
Version: 1.0-beta
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
		global $wp_embed;

		// make_clickable breaks oEmbed regex, make sure we go earlier
		$clickable = has_filter( 'comment_text', 'make_clickable' );
		$priority = ( $clickable ) ? $clickable - 1 : 10;

		add_filter( 'comment_text', array( $wp_embed, 'autoembed' ), $priority );
	}
}

new ES_oEmbed_Comments;
