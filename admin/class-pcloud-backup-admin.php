<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://josselynjayant.fr
 * @since      1.0.0
 *
 * @package    Pcloud_Backup
 * @subpackage Pcloud_Backup/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pcloud_Backup
 * @subpackage Pcloud_Backup/admin
 * @author     Josselyn Jayant <zuidhoekmike@gmail.com>
 */
class Pcloud_Backup_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        $this->load_dependencies();

    }
    
    /**
	 * Load the required dependencies for the Admin facing functionality.
	 *
	 * @since  1.0.0
     * 
     * @return void
	 */
	private function load_dependencies() {

        require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-pcloud-backup-folder.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-pcloud-backup-backup.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-pcloud-backup-backup-request.php';
        
        require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-plcoud-backup-plugin-settings.php';
        
		require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-pcloud-backup-backup-page.php';

    }
}
