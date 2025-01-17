<?php
/**
 * JWR Shared Options
 *
 * @package JWR_AME_Styler
 * @author Josh Robbs <josh@joshrobbs.com>
 *
 * @wordpress-plugin
 * Plugin Name:       JWR AME Styler
 * Plugin URI:        https://joshrobbs.com
 * Description:       Style options for Advance Menu Styler
 * Version:           0.2.0
 * Author:            Josh Robbs <josh@joshrobbs.com>
 * Author URI:        https://joshrobbs.com
 * Text Domain:       jwr
 * Requires PHP:      8.0
 */

namespace jwr_ame_styler;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/shared-functions/shared-utils.php';





/**
 * Add updates from GitHub.
 *
 * @param object $transient The plugin update transient.
 * @return object The plugin update transient.
 */
function my_plugin_check_for_updates( $transient ) {
	if ( empty( $transient->checked ) ) {
		return $transient;
	}

	// Get current plugin version and URI dynamically.
	$plugin_data     = get_plugin_data( __FILE__ ); // __FILE__ is the main plugin file.
	$current_version = $plugin_data['Version'];
	$plugin_uri      = $plugin_data['PluginURI'];

	if ( is_null( $current_version ) ) {
		$current_version = '0.1';
	}

	$response = wp_remote_get( 'https://raw.githubusercontent.com/jwrobbs/jwr-shared-options/main/plugin-update.json' );

	if ( is_wp_error( $response ) ) {
		return $transient;
	}

	$update_data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( version_compare( $update_data['version'], $current_version, '>' ) ) {
		$transient->response['jwr-shared-options/index.php'] = (object) array(
			'new_version' => $update_data['version'],
			'package'     => $update_data['download_url'],
			'slug'        => 'jwr-shared-options',
			'url'         => $plugin_uri,
		);
	}

	return $transient;
}
	add_filter( 'site_transient_update_plugins', __NAMESPACE__ . '\my_plugin_check_for_updates' );
