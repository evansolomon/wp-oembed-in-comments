<?php

/*
Plugin Name: oEmbed in Comments
Description: Allow oEmbeds in comment text
Version: 1.0-alpha
Author: Evan Solomon
Author URI: http://evansolomon.me
*/

class ES_oEmbed_Comments {
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		if ( ! get_option( 'embed_autourls' ) )
			return;

		$this->oembed_in_comments();
	}

	function oembed_in_comments() {
		global $wp_embed;

		// make_clickable breaks oEmbed regex
		$clickable = has_filter( 'comment_text', 'make_clickable' );
		$priority = ( $clickable ) ? $clickable - 1 : 10;
		add_filter( 'comment_text', array( $wp_embed, 'autoembed' ), $priority );

		// wp_kses_post will clobber the markup that oEmbed gave us
		$kses_post = has_filter( 'comment_text', 'wp_kses_post' );
		if ( ! $kses_post )
			return;

		// Move wp_kses_post to before autoembed
		remove_filter( 'comment_text', 'wp_kses_post' );
		add_filter( 'comment_text', 'wp_kses_post', $priority - 1 );
	}
}

new ES_oEmbed_Comments;
