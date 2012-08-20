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
	 * Setup filters to do oEmbed in comments
	 */
	function oembed_in_comments() {
		global $wp_embed;

		// wp_kses_post will clobber the markup that oEmbed gave us, make sure we go later
		$kses = has_filter( 'comment_text', 'wp_kses_post' );

		// make_clickable breaks oEmbed regex, make sure we go earlier
		$clickable = has_filter( 'comment_text', 'make_clickable' );

		if ( ! $clickable && ! $kses ) {
			$priority = 10;
		}
		elseif ( ! $kses ) {
			$priority = $clickable - 1;
		}
		elseif ( ! $clickable || $clickable > $kses ) {
			$priority = $kses;
		}
		else {
			// Move make_clickable later
			remove_filter( 'comment_text', 'make_clickable', $clickable );
			add_filter( 'comment_text', 'make_clickable', $kses + 1 );

			$priority = $kses;
		}

		add_filter( 'comment_text', array( $wp_embed, 'autoembed' ), $priority );
	}

	/**
	 * Setup admin area
	 */
	function admin_init() {
		$this->register_setting();
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
			array( $this, 'sanitize_input' )
		);
	}

	/**
	 * Check box to turn oEmbed on or off in comments
	 */
	function setting_field() {
		$output  = sprintf( '<label for="%s">', esc_attr( $this->get_option_name() ) );
		$output .= sprintf( '<input name="%s" type="checkbox" id="%s" %s /> %s',
			esc_attr( $this->get_option_name() ),
			esc_attr( $this->get_option_name() ),
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
	 * Always cast the input as a boolean
	 */
	function sanitize_input( $input ) {
		return (bool) 'on' == $input;
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
