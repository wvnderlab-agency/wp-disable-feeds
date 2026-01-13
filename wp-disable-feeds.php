<?php

/*
 * Plugin Name:     Disable Feeds
 * Plugin URI:      https://github.com/wvnderlab-agency/wp-disable-feeds/
 * Author:          Wvnderlab Agency
 * Author URI:      https://wvnderlab.com
 * Text Domain:     wvnderlab-disable-feeds
 * Version:         0.1.0
 */

/*
 *  ################
 *  ##            ##    Copyright (c) 2025 Wvnderlab Agency
 *  ##
 *  ##   ##  ###  ##    ✉️ moin@wvnderlab.com
 *  ##    #### ####     🔗 https://wvnderlab.com
 *  #####  ##  ###
 */

declare(strict_types=1);

namespace WvnderlabAgency\DisableFeeds;

defined( 'ABSPATH' ) || die;

// Return early if running in WP-CLI context.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	return;
}

/**
 * Filter: Disable Feeds Enabled
 *
 * @param bool $enabled Whether to enable the disable feeds functionality. Default true.
 * @return bool
 */
if ( ! apply_filters( 'wvnderlab/disable-feeds/enabled', true ) ) {
	return;
}

/**
 * Disable Feeds
 *
 * @link   https://developer.wordpress.org/reference/hooks/do_feed/
 * @hooked action do_feed
 * @hooked action do_feed_atom
 * @hooked action do_feed_atom_comments
 * @hooked action do_feed_rdf
 * @hooked action do_feed_rss
 * @hooked action do_feed_rss2
 * @hooked action do_feed_rss2_comments
 *
 * @return void
 */
function disable_feed(): void {
	/**
	 * Filter: Disable Feeds Status Code
	 *
	 * Supported:
	 * - 301 / 302 / 307 / 308  → redirect
	 * - 404 / 410              → no redirect, proper error response
	 *
	 * @param int $status_code The HTTP status code for the redirect. Default is 404 (Not Found).
	 * @return int
	 */
	$status_code = (int) apply_filters(
		'wvnderlab/disable-feeds/status-code',
		404
	);

	if ( ! in_array( $status_code, array( 404, 410 ), true ) ) {
		$status_code = 404;
	}

	wp_die(
		esc_html__( 'Feeds are disabled on this site.', 'wvnderlab-disable-feeds' ),
		esc_html__( 'Feeds Disabled', 'wvnderlab-disable-feeds' ),
		array( 'response' => esc_html( $status_code ) )
	);
}

add_action( 'do_feed', __NAMESPACE__ . '\\disable_feed', PHP_INT_MIN );
add_action( 'do_feed_atom', __NAMESPACE__ . '\\disable_feed', PHP_INT_MIN );
add_action( 'do_feed_atom_comments', __NAMESPACE__ . '\\disable_feed', PHP_INT_MIN );
add_action( 'do_feed_rdf', __NAMESPACE__ . '\\disable_feed', PHP_INT_MIN );
add_action( 'do_feed_rss', __NAMESPACE__ . '\\disable_feed', PHP_INT_MIN );
add_action( 'do_feed_rss2', __NAMESPACE__ . '\\disable_feed', PHP_INT_MIN );
add_action( 'do_feed_rss2_comments', __NAMESPACE__ . '\\disable_feed', PHP_INT_MIN );

/**
 * Remove Feed Links from wp_head
 *
 * @link   https://developer.wordpress.org/reference/hooks/init/
 * @hooked action init
 * @return void
 */
function remove_actions_hooks(): void {
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
}

add_action( 'init', __NAMESPACE__ . '\\remove_actions_hooks', PHP_INT_MAX );

/**
 * Unregister Meta Widget
 *
 * @link   https://developer.wordpress.org/reference/hooks/widgets_init/
 * @hooked action widgets_init
 *
 * @return void
 */
function unregister_meta_widget(): void {
	unregister_widget( 'WP_Widget_Meta' );
	unregister_widget( 'WP_Widget_RSS' );
}

add_action( 'widgets_init', __NAMESPACE__ . '\\unregister_meta_widget', PHP_INT_MAX );
