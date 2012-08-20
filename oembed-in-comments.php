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
		add_action( 'init',       array( $this, 'init'       ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	function init() {
		if ( ! $this->is_enabled() )
			return;

		$this->oembed_in_comments();
	}

	function admin_init() {
		$this->register_setting();
	}

	function oembed_in_comments() {
		global $wp_embed;

		// make_clickable breaks oEmbed regex
		$clickable = has_filter( 'comment_text', 'make_clickable' );
		$priority = ( $clickable ) ? $clickable - 1 : 10;
		add_filter( 'comment_text', array( $wp_embed, 'autoembed' ), $priority );

		// wp_kses_post will clobber the markup that oEmbed gave us
		$kses_filter = has_filter( 'comment_text', 'wp_kses_post' );
		if ( ! $kses_filter )
			return;

		// Move wp_kses_post to before autoembed
		remove_filter( 'comment_text', 'wp_kses_post', $kses_filter );
		add_filter( 'comment_text', 'wp_kses_post', $priority - 1 );
	}

	function register_setting() {
		add_settings_field( $this->get_option_name(), __( 'Auto-embeds in comments' ), array( $this, 'setting_field' ), 'media', 'embeds' );
		register_setting( 'media', $this->get_option_name(), array( $this, 'sanitize_option' ) );
	}

	function setting_field() {
		$output  = '<label for="comment_embed_autourls">';
		$output .= sprintf( '<input name="comment_embed_autourls" type="checkbox" id="comment_embed_autourls" %s/> %s',
			checked( true, $this->is_enabled(), false ),
			esc_html__( 'When possible, embed the media content from a URL directly into comments.' )
		);
		$output .= '</label>';

		echo $output;
	}

	function get_option_name() {
		return __CLASS__ . '_enabled';
	}

	function sanitize_option( $setting ) {
		return (bool) $setting;
	}

	function is_enabled() {
		$enabled = get_option( $this->get_option_name(), null );

		// If there's no option set, default to the option for posts
		if ( is_null( $enabled ) )
			$enabled = get_option( 'embed_autourls' );

		return (bool) $enabled;
	}
}

new ES_oEmbed_Comments;