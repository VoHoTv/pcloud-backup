<?php

/**
 * The settings of the plugin.
 *
 * @link       http://devinvinson.com
 * @since      1.0.0
 *
 * @package    Pcloud_Backup
 * @subpackage Pcloud_Backup/admin
 */

use pCloud\App;

/**
 * Class WordPress_Plugin_Template_Settings
 *
 */
class Pcloud_Backup_Plugin_Settings {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
    private $pcloud;
    
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( pCloud\App $pcloud ) {

		$this->pcloud = $pcloud;

	}

	public function setup_plugin_options_menu() {

        add_submenu_page(
            'pcloud-backup', 
            __('Settings', 'pcloud-backup'), 
            __('Settings', 'pcloud-backup'), 
            'manage_options', 
            'pcloud-backup-settings', 
            array( $this, 'render_settings_page_content')
        );

	}

    /**
     * Provide default values for the API settings..
     * 
     * @since 2.0.0.
     * 
     * @return $defaults
     */
	public function default_api_settings() {
		$defaults = array(
			'app_key'		=>	'',
			'app_secret'	=>	'',
			'redirect_uri'	=>	'',
		);

		return $defaults;
	}

	public function render_settings_page_content() {
		?>
		<div class="wrap">

			<h2><?php _e( 'pCloud Backup Settings', 'pcloud-backup-plugin' ); ?></h2>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php

					settings_fields( 'pcloud_backup_api_options' );
					do_settings_sections( 'pcloud_backup_api_options' );

				submit_button();

				?>
			</form>

		</div>
	<?php
	}

	public function api_settings_callback() {
		echo '<p>' . __( 'You can find your API credentials', 'pcloud-backup-plugin' ) . ' <a target="_blank" href="https://pcloud.com/">' . __('Here', 'pcloud-backup') . '</a>' . '</p>';
    }
    
    /**
     * Initializes the plugin settings.
     * 
     * @since 2.0.0.
     * 
     * @return void
     */
	public function initialize_api_settings() {
        if( false == get_option( 'pcloud_backup_api_options' ) ) {
			$default_array = $this->default_api_settings();
			update_option( 'pcloud_backup_api_options', $default_array );
		}

		add_settings_section(
			'api_settings_section',
			__( 'API Settings', 'pcloud-backup-plugin' ),
			array( $this, 'api_settings_callback'),
			'pcloud_backup_api_options'
		);

		add_settings_field(
			'client_id',
			__('Client ID', 'pcloud-backup'),
			array( $this, 'client_id_callback'),
			'pcloud_backup_api_options',
			'api_settings_section'
		);

		add_settings_field(
			'client_secret',
			__('Client Secret', 'pcloud-backup'),
			array( $this, 'client_secret_callback'),
			'pcloud_backup_api_options',
			'api_settings_section'
		);

		add_settings_field(
			'redirect_uri',
			__('Redirect URI', 'pcloud-backup'),
			array( $this, 'redirect_uri_callback'),
			'pcloud_backup_api_options',
			'api_settings_section'
		);

		register_setting(
			'pcloud_backup_api_options',
			'pcloud_backup_api_options',
			array( $this, 'sanitize_api_settings')
		);
	}

	public function client_id_callback() {
		$options = get_option( 'pcloud_backup_api_options' );

		$client_id = '';
		if( isset( $options['client_id'] ) ) {
			$client_id = esc_attr( $options['client_id'] );
		}

		echo '<input type="text" id="client_id" name="pcloud_backup_api_options[client_id]" value="' . $client_id . '" />';
	}

	public function client_secret_callback() {
		$options = get_option( 'pcloud_backup_api_options' );

		$client_secret = '';
		if( isset( $options['client_secret'] ) ) {
			$client_secret = esc_attr( $options['client_secret'] );
		}

		echo '<input type="text" id="client_secret" name="pcloud_backup_api_options[client_secret]" value="' . $client_secret . '" />';
	}

	public function redirect_uri_callback() {
		echo '<input type="url" disabled value="' . menu_page_url('pcloud-backup-settings', false) . '" />';
	}

    /**
     * Sanitization callback for the API settings. Since each of the API settings options are text inputs.
     * 
     * @since 2.0.0.
     * 
     * @param  array  $input
     * @return $output
     */
	public function sanitize_api_settings( $input ) {
		$options = get_option( 'pcloud_backup_api_options' );

        $output = array();

		foreach( $input as $key => $val ) {

			if( isset ( $input[$key] ) ) {
				$output[$key] = sanitize_text_field( strip_tags( stripslashes( $input[$key] ) ) );
			} // end if

        } 
        
		return $output;
    }
    
    /**
     * Get the authorization code.
     * 
     * @since 2.0.0.
     * 
     * @return void
     */
    public function get_authorization_code()
    {
        $options = get_option( 'pcloud_backup_api_options' );

        $client_id= $options['client_id'];
        $client_secret= $options['client_secret'];
        $redirect_uri= menu_page_url('pcloud-backup-settings', false);

        if(!$client_id || !$client_secret || !$redirect_uri) return;

        $this->pcloud->setAppKey($client_id);
        $this->pcloud->setAppSecret($client_secret);
        
        $this->pcloud->setRedirectURI($redirect_uri);
    
        $codeUrl = $this->pcloud->getAuthorizeCodeUrl();

        wp_redirect($codeUrl); die;
    }

    /**
     * Update the access token when the authorization code is present.
     * 
     * @since 2.0.0.
     * 
     * @return void
     */
    public function update_access_token()
    {
        if(!isset($_GET['code'])) return;

        $options = get_option( 'pcloud_backup_api_options' );

        $client_id= $options['client_id'];
        $client_secret= $options['client_secret'];
        $redirect_uri= menu_page_url('pcloud-backup-settings', false);

        $this->pcloud->setRedirectURI($redirect_uri);
        $this->pcloud->setAppKey($client_id);
        $this->pcloud->setAppSecret($client_secret);

        $token = $this->pcloud->getTokenFromCode($_GET['code'], $_GET['locationid']);

        update_option('pcloud_backup_access_token', $token['access_token']);
        update_option('pcloud_backup_Location_id', $token['locationid']);

        wp_safe_redirect($redirect_uri);
    }

}