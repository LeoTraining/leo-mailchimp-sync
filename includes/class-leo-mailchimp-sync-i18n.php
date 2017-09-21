<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       natehobi.com
 * @since      1.0.0
 *
 * @package    Leo_Mailchimp_Sync
 * @subpackage Leo_Mailchimp_Sync/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Leo_Mailchimp_Sync
 * @subpackage Leo_Mailchimp_Sync/includes
 * @author     Nate Hobi <nate@natehobi.com>
 */
class Leo_Mailchimp_Sync_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'leo-mailchimp-sync',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
