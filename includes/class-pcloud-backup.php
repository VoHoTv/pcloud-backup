<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://josselynjayant.fr
 * @since      1.0.0
 *
 * @package    Pcloud_Backup
 * @subpackage Pcloud_Backup/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Pcloud_Backup
 * @subpackage Pcloud_Backup/includes
 * @author     Josselyn Jayant <zuidhoekmike@gmail.com>
 */
class Pcloud_Backup {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Pcloud_Backup_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PCLOUD_BACKUP_VERSION' ) ) {
			$this->version = PCLOUD_BACKUP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'pcloud-backup';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Pcloud_Backup_Loader. Orchestrates the hooks of the plugin.
	 * - Pcloud_Backup_i18n. Defines internationalization functionality.
	 * - Pcloud_Backup_Admin. Defines all hooks for the admin area.
	 * - Pcloud_Backup_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pcloud-backup-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pcloud-backup-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pcloud-backup-admin.php';

		$this->loader = new Pcloud_Backup_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Pcloud_Backup_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Pcloud_Backup_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

        $options = get_option( 'pcloud_backup_api_options' );

        $plugin_admin = new Pcloud_Backup_Admin( $this->get_plugin_name(), $this->get_version() );
        
        $pcloud_app = new pCloud\App();
        $pcloud_app->setAccessToken(get_option('pcloud_backup_access_token'));
        $pcloud_app->setLocationId(get_option('pcloud_backup_Location_id'));

        $pcloud_backup_folders = new Pcloud_Backup_Folder($pcloud_app);
        $pcloud_backup_backup = new Pcloud_Backup_Backup(new Pcloud_Backup_Backup_Request(new pCloud\File($pcloud_app), new ZipArchive()));
        
        $this->loader->add_action('wp_ajax_create_folder', $pcloud_backup_folders, 'create_folder');
        $this->loader->add_action('wp_ajax_get_root_folders', $pcloud_backup_folders, 'get_root_folders');
        $this->loader->add_action('wp_ajax_get_child_folders', $pcloud_backup_folders, 'get_child_folders');

        $this->loader->add_action('wp_ajax_upload_backup', $pcloud_backup_backup, 'create_backup');

        /**
         * Backup page
         */
        $backup_page = new Pcloud_Backup_Backup_Page( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_menu', $backup_page, 'add_backup_page' );
        
        /**
         * Settings page
         */
		$plugin_settings = new Pcloud_Backup_Plugin_Settings( $pcloud_app );

        $this->loader->add_action( 'admin_menu', $plugin_settings, 'setup_plugin_options_menu' );
        $this->loader->add_action( 'admin_init', $plugin_settings, 'initialize_api_settings' );

		$this->loader->add_action( 'update_option_pcloud_backup_api_options', $plugin_settings, 'get_authorization_code' );
		$this->loader->add_action( 'admin_init', $plugin_settings, 'update_access_token' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Pcloud_Backup_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
