<?php

/**
 * The settings of the plugin.
 *
 * @link       https://josselynjayant.fr
 * @since      1.0.0
 *
 * @package    Pcloud_Backup
 * @subpackage Pcloud_Backup/admin
 */

class Pcloud_Backup_Backup_Page {

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
	public function __construct( $plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function add_backup_page() {

        add_menu_page(
            __('pCloud Backup', 'pcloud-backup'), 
            __('pCloud Backup', 'pcloud-backup'),
            'manage_options',
            'pcloud-backup',
            array( $this, 'render_backup_page_content'),
            'dashicons-cloud-saved',
        );

        add_submenu_page(
            'pcloud-backup',
            __('Backup', 'pcloud-backup'), 
            __('Backup', 'pcloud-backup'),
            'manage_options',
            'pcloud-backup',
            array( $this, 'render_backup_page_content'),
        );

    }
    
    private function enqueue_assets() {

		wp_enqueue_style( 'fancytree', '//cdn.jsdelivr.net/npm/jquery.fancytree@2.27/dist/skin-win8/ui.fancytree.min.css');
		wp_enqueue_style( 'smart-wizard', plugin_dir_url( __FILE__ ) . 'css/smart_wizard_all.min.css');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pcloud-backup-admin.css', array(), $this->version );

		wp_enqueue_script( 'fancytree', '//cdn.jsdelivr.net/npm/jquery.fancytree@2.27/dist/jquery.fancytree-all-deps.min.js' );
		wp_enqueue_script( 'smart-wizard', plugin_dir_url( __FILE__ ) . 'js/jquery.smartWizard.min.js', array( 'jquery' ), false, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pcloud-backup-admin.js', array( 'jquery' ), $this->version, false );

        wp_localize_script($this->plugin_name, 'pCloudBackup', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));
    }

	public function render_backup_page_content() {
        
        $this->enqueue_assets();
        ?>
        <div id="pcloud-backup-wizard-wrapper">
        <div id="pcloud-backup-wizard">
            <ul class="nav">
            <li>
                <a class="nav-link" href="#step-1">
                    <?php _e('Ready?', 'pcloud-backup') ?>
                </a>
            </li>
            <li>
                <a class="nav-link" href="#step-2">
                    <?php _e('What?', 'pcloud-backup') ?>
                </a>
            </li>
            <li>
                <a class="nav-link" href="#step-3">
                    <?php _e('Where?', 'pcloud-backup') ?>
                </a>
            </li>
            <li>
                <a class="nav-link" href="#step-4">
                    <?php _e('Uploading', 'pcloud-backup') ?>
                </a>
            </li>
            </ul>
        
            <div class="tab-content">
            <div id="step-1" class="tab-pane" role="tabpanel">
                <?php echo sprintf(__('Hello %s, ready to back up your site?', 'pcloud-backup'), wp_get_current_user()->display_name); ?>
            </div>
            <div id="step-2" class="tab-pane" role="tabpanel">
                <?php _e('What would you like to backup?', 'pcloud-backup') ?>
                <label class="pcloud-backup-type-checkbox">
                    <input type="checkbox" id="pcloud_backup_files" >
                    <?php _e('Folders/Files', 'pcloud-backup'); ?>
                </label>
                <label class="pcloud-backup-type-checkbox">
                    <input type="checkbox" id="pcloud_backup_database" > 
                    <?php _e('Database', 'pcloud-backup'); ?>
                </label>
            </div>
            <div id="step-3" class="tab-pane" role="tabpanel">
                <div id="tree">
                </div>
            </div>
            <div id="step-4" class="tab-pane" role="tabpanel">
                <div class="sk-folding-cube">
                    <div class="sk-cube1 sk-cube"></div>
                    <div class="sk-cube2 sk-cube"></div>
                    <div class="sk-cube4 sk-cube"></div>
                    <div class="sk-cube3 sk-cube"></div>
                </div>
                <?php echo sprintf(__('Lay back and relax while we make the backup.', 'pcloud-backup')); ?>
            </div>
            </div>
        </div>
        </div>
	<?php
	}


}