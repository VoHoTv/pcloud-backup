<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://josselynjayant.fr
 * @since      1.0.0
 *
 * @package    Pcloud_Backup
 * @subpackage Pcloud_Backup/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Pcloud_Backup
 * @subpackage Pcloud_Backup/includes
 * @author     Josselyn Jayant <zuidhoekmike@gmail.com>
 */
class Pcloud_Backup_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'pcloud-backup',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
