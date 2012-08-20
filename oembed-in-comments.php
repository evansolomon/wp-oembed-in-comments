<?php

/*
Plugin Name: oEmbed in Comments
Description: Allow oEmbeds in comment text
Version: 1.0-alpha
Author: Evan Solomon
Author URI: http://evansolomon.me
*/

class ES_oEmbed_Comments {
	/**
	 * Add generic actions
	 */
	function __construct() {
		add_action( 'init',       array( $this, 'init'       ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * If oEmbed is enabled in comments, keep going
	 */
	function init() {
		if ( ! $this->is_enabled() )
			return;

		$this->oembed_in_comments();
	}

	/**
	 * Setup admin area
	 */
	function admin_init() {
		$this->register_setting();
	}

	/**
	 * Setup filters to do oEmbed in comments
	 */
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

	/**
	 * Register setting and settings field to allow enabling or disabling
	 */
	function register_setting() {
		add_settings_field(
			$this->get_option_name(),
			__( 'Auto-embeds in comments' ),
			array( $this, 'setting_field' ),
			'media',
			'embeds'
		);

		register_setting(
			'media',
			$this->get_option_name(),
			array( $this, 'sanitize_option' )
		);

	}

	/**
	 * Check box to turn oEmbed on or off in comments
	 */
	function setting_field() {
		$field_id = $this->get_option_name();

		$output  = sprintf( '<label for="%s">', esc_attr( $field_id ) );
		$output .= sprintf( '<input name="%s" type="checkbox" id="%s" %s /> %s',
			esc_attr( $field_id ),
			esc_attr( $field_id ),
			checked( true, $this->is_enabled(), false ),
			esc_html__( 'When possible, embed the media content from a URL directly into comments.' )
		);
		$output .= '</label>';

		echo $output;
	}

	/**
	 * Wrapper to generate an option name based on the class name
	 */
	function get_option_name() {
		return strtolower( __CLASS__ ) . '_enabled';
	}

	/**
	 * Always cast our option as a boolean
	 */
	function sanitize_option( $setting ) {
		if ( 'on' == $setting )
			return true;
		else
			return false;
	}

	/**
	 * Check whether oEmbed is enabled or disabled in comments
	 */
	function is_enabled() {
		$enabled = get_option( $this->get_option_name(), null );

		// If there's no option set, default to the option for posts
		if ( is_null( $enabled ) )
			$enabled = get_option( 'embed_autourls' );

		return (bool) $enabled;
	}
}

new ES_oEmbed_Comments;