<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://josselynjayant.fr
 * @since             1.0.0
 * @package           Pcloud_Backup
 *
 * @wordpress-plugin
 * Plugin Name:       pCloud Backup
 * Plugin URI:        https:/josselynjayant.fr/pcloud-backup-wordpress-plugin
 * Description:       Seamlessly backup your WordPress website to pCloud.
 * Version:           2.0.0
 * Author:            Josselyn Jayant
 * Author URI:        https://josselynjayant.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pcloud-backup
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PCLOUD_BACKUP_VERSION', '2.0.0' );

/**
 * Require the pCloud SDK autoloader.
 */
require_once plugin_dir_path( __FILE__ ) . 'lib/pCloud/autoload.php';

/**
 * Require the MYSQLDump class.
 */
require_once plugin_dir_path( __FILE__ ) . 'lib/MySQLDump.php';

/**
 * Require the wp-async-request.php class.
 */
require_once plugin_dir_path( __FILE__ ) . 'lib/wp-async-request.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pcloud-backup-activator.php
 */
function activate_pcloud_backup() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pcloud-backup-activator.php';
	Pcloud_Backup_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pcloud-backup-deactivator.php
 */
function deactivate_pcloud_backup() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pcloud-backup-deactivator.php';
	Pcloud_Backup_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pcloud_backup' );
register_deactivation_hook( __FILE__, 'deactivate_pcloud_backup' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pcloud-backup.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pcloud_backup() {

	$plugin = new Pcloud_Backup();
	$plugin->run();

}
run_pcloud_backup();
