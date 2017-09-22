<?php


use \DrewM\MailChimp\MailChimp;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       natehobi.com
 * @since      1.0.0
 *
 * @package    Leo_Mailchimp_Sync
 * @subpackage Leo_Mailchimp_Sync/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Leo_Mailchimp_Sync
 * @subpackage Leo_Mailchimp_Sync/admin
 * @author     Nate Hobi <nate@natehobi.com>
 */
class Leo_Mailchimp_Sync_Admin {

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

	private $list_id = '834a4705f2';

	private $key = '5fcc5f3bde44d3a48cc5039f93d79998-us2';	

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
		$this->interests = array(
			'admin' => '515b10ab47',
			'courtSmart' => 'fcdf4edec1',
			'freeUser' => 'bca1ee14b8',
			'deleted' => 'f711e9a540'
		);

		$this->mc = new MailChimp($this->key);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Leo_Mailchimp_Sync_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leo_Mailchimp_Sync_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/leo-mailchimp-sync-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Leo_Mailchimp_Sync_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leo_Mailchimp_Sync_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/leo-mailchimp-sync-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	public function add_user($user_id) {
		$subscriber_hash = $this->mc->subscriberHash('test@example.com');	
		$user = get_user_by('ID', $user_id);
		$result = null;

		$interests = [
			$this->interests['admin'] => (bool) get_user_meta($user->ID, '_is_department_head', true),
			$this->interests['courtSmart'] => in_array('s2member_level4', $user->roles),
			$this->interests['freeUser'] => !in_array('s2member_level4', $user->roles),
			$this->interests['deleted'] => false
		];

		
		if(true) {
			echo '<pre>'; var_dump($user->first_name); echo '</pre>'; exit();
			$result = $this->mc->post("lists/$this->list_id/members", [
				'email_address' => $user->user_email,
				'status'        => 'subscribed',
				'merge_fields' => [
					'FNAME' => $user->first_name, 
					'LNAME' => $user->last_name
				],
				'interests'    => $interests
			]);			
			
		} else {
			$result = $this->mc->patch("lists/$this->list_id/members/$subscriber_hash", [			
				'interests' => $interests
			]);
		}

		echo '<pre>'; var_dump($result); echo '</pre>'; exit();
	}


	public function update_membership_level_interests($email, $isPaidUser) {
		if($isPaidUser) {		
			$this->add_to_courtsmart_group($email);
			$this->remove_from_free_group($email);
		} else {
			$this->add_to_free_group($email);
			$this->remove_from_courtsmart_group($email);	
		}
	}

	public function delete_user($user_id) {
		$user = get_user_by('ID', $user_id);

		$this->remove_from_admin_group($user->user_email);
		$this->remove_from_free_group($user->user_email);
		$this->remove_from_courtsmart_group($user->user_email);
		$this->add_to_deleted_group($user->user_email);
	}

	public function update_is_admin($id, $user_id, $key, $value) {
		
		$email = $old->user_email;
		$userIsAdmin = false;
		$isFreeUser = false;

		if($userIsAdmin) {
			$this->add_to_admin_group($email);
		} else {
			$this->remove_from_deleted_group($email);
		}
	}

	public function update_level($id, $user_id, $key, $value) {		
		if($key === 'wp_capabilities') {
			$email = get_user_by('ID', $user_id)->user_email;
			$isPaidUser = array_key_exists('s2member_level4', $value) && $value['s2member_level4'];
			$this->update_membership_level_interests($email, $isPaidUser);		
		}		
	}	

	public function add_to_deleted_group($email) {
		$interest = $this->interests['deleted'];		
		$this->update_interest($interest, $email, true);
	}

	public function remove_from_deleted_group($email) {
		$interest = $this->interests['deleted'];	
		$this->update_interest($interest, $email, false);
	}

	public function add_to_admin_group($email) {
		$interest = $this->interests['admin'];	
		$this->update_interest($interest, $email, true);
	}

	public function remove_from_admin_group($email) {
		$interest = $this->interests['admin'];
		$this->update_interest($interest, $email, false);
	}

	public function add_to_courtsmart_group($email) {
		$interest = $this->interests['courtSmart'];
		$this->update_interest($interest, $email, true);
	}

	public function remove_from_courtsmart_group($email) {
		$interest = $this->interests['courtSmart'];
		$this->update_interest($interest, $email, false);
	}

	public function add_to_free_group($email) {
		$interest = $this->interests['freeUser'];
		$this->update_interest($interest, $email, true);
	}

	public function remove_from_free_group($email) {
		$interest = $this->interests['freeUser'];
		$this->update_interest($interest, $email, false);
	}

	public function update_interest($interest, $email, $toggle) {
		$subscriber_hash = $this->mc->subscriberHash($email);
		$interests = $this->mc->get("lists/$this->list_id/members/$subscriber_hash")['interests'];
		$interests[$interest] = $toggle;		
		$result = $this->mc->patch("lists/$this->list_id/members/$subscriber_hash", [			
			'interests' => $interests
		]);

		echo '<pre>'; var_dump($result); echo '</pre>'; exit();
	}
}
