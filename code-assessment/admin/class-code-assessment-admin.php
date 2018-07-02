<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wordpressguru.net
 * @since      1.0.0
 *
 * @package    Code_Assessment
 * @subpackage Code_Assessment/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Code_Assessment
 * @subpackage Code_Assessment/admin
 * @author     Carlos Reyes <sitesbycarlos@gmail.com>
 */
class Code_Assessment_Admin {

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
		 * defined in Code_Assessment_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Code_Assessment_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name.'dataTables', plugin_dir_url( __FILE__ ) . 'css/jquery.dataTables.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'admin', plugin_dir_url( __FILE__ ) . 'css/code-assessment-admin.css', array(), $this->version, 'all' );

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
		 * defined in Code_Assessment_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Code_Assessment_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name.'dataTables', plugin_dir_url( __FILE__ ) . 'js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'admin', plugin_dir_url( __FILE__ ) . 'js/code-assessment-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->plugin_name.'admin', 'ajax_post_object', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'tokens' => array(
				'default' => wp_create_nonce('ca-default'),
			)
		));
	}

	function bad_status_codes() {
		return array( 404, 500 );
	}

	function check_response($response) {
		if (! is_array($response)) {
			return false;
		}
		if (is_wp_error($response)) {
			return false;
		}
		if (! isset($response['response'])) {
			return false;
		}
		if (! isset($response['response']['code'])) {
			return false;
		}
		if (in_array($response['response']['code'], $this->bad_status_codes(), true)) {
			return false;
		}

		return $response['response']['code'];
	}


	function default_sites() {
		$sites = array();

		$sites['orlando'] = array('url'=>'https://2018.orlando.wordcamp.org','site_name'=>'Orlando WordCamp');
		$sites['denvar'] = array('url'=>'https://2018.denver.wordcamp.org','site_name'=>'Denver WordCamp');
		$sites['pokhara'] = array('url'=>'https://2018.pokhara.wordcamp.org','site_name'=>'Pokhara WordCamp');

		return $sites;
	}

	/**
	 * Get Data From Different Site
	 */
	function get_most_recently_published_posts_data( $site_key = 'all' ) {

		$get_sites = $this->default_sites();
		$number_of_posts = !empty(get_option( 'number_of_posts' )) ? get_option( 'number_of_posts' ) : 10;
		if( 'all' === $site_key ) {
			$transient = get_transient('code_assessment_most_recent_posts_'.$site_key);
			$transient = '';
			if (! empty($transient)) {
				return json_decode($transient, true);
			} else {
				$counter = 0 ;
				$all_recent_posts = '';
				$request_args = array( 'sslverify' => false, 'timeout' => 60 );
				foreach( $get_sites as $key => $site ) {
					$site_url = $site['url'];
					$api_url = $site_url.'/wp-json/wp/v2/posts?per_page='.$number_of_posts.'&order=desc';
					$api_response = wp_remote_get($api_url, $request_args);
					if ($this->check_response($api_response)) {
						$recent_posts = json_decode($api_response['body'], true);
						set_transient('code_assessment_most_recent_posts_'.$key, wp_json_encode($recent_posts), DAY_IN_SECONDS);
						if($counter > 0) {
							$all_recent_posts = array_merge($all_recent_posts, $recent_posts);
						} else {
							$all_recent_posts = $recent_posts;
						}
						$counter++;
					}
				}
				set_transient('code_assessment_most_recent_posts_all', wp_json_encode($all_recent_posts), DAY_IN_SECONDS);
				return $all_recent_posts;
			}
		} else {
			$transient = get_transient('code_assessment_most_recent_posts_'.$site_key);
			$transient = '';
			if (! empty($transient)) {
				return json_decode($transient, true);
			} else {
				$site_url = $get_sites[$site_key]['url'];
				$api_url = $site_url.'/wp-json/wp/v2/posts?per_page='.$number_of_posts.'&order=desc';
				$request_args = array( 'sslverify' => false, 'timeout' => 60 );
				$api_response = wp_remote_get($api_url, $request_args);
				if ($this->check_response($api_response)) {
					$recent_posts = json_decode($api_response['body'], true);
					set_transient('code_assessment_most_recent_posts_'.$site_key, wp_json_encode($recent_posts), DAY_IN_SECONDS);
					return $recent_posts;
				}

				if (is_wp_error($api_response)) {
					return  $api_response;
				}
				return false;
			}
		}

	}


	public function make_admin_menu() {
		add_menu_page('Code Assessment', 'Code Assessment', 'manage_options', 'code-assessment', array($this, 'admin_menu_page_callback') );
	}

	public function admin_menu_page_callback() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/code-assessment-admin-display.php';
	}

	public function update_admin_menu_info() {
		register_setting( 'code-assessment-settings', 'number_of_posts' );
	}

	public function show_dashboard_posts() {
		wp_add_dashboard_widget( 'code_assessment_recent_posts', __( 'Recent Posts' ), array($this, 'code_assessment_recent_posts_callback') );
	}

	public function code_assessment_recent_posts_callback() {
		$active_tab = 'all';
		if( isset( $_GET[ 'ca-recent-posts-tab' ] ) ) {
			$active_tab = $_GET[ 'ca-recent-posts-tab' ];
		}
		$get_recent_posts = $this->get_most_recently_published_posts_data($active_tab);
		$get_sites = $this->default_sites();
		$admin_url = get_admin_url();
		?>
		<h2 class="code-assessment-recent-posts-nav nav-tab-wrapper">
			<a href="<?php echo esc_url($admin_url)?>" class="nav-tab <?php echo $active_tab == 'all' ? 'nav-tab-active' : ''; ?>">Default</a>
			<?php
			foreach( $get_sites as $key => $site ) {
				?>
				<a href="<?php echo esc_url($admin_url)?>?ca-recent-posts-tab=<?php echo $key;?>" class="nav-tab <?php echo $active_tab == $key ? 'nav-tab-active' : ''; ?>"><?php echo esc_html($site['site_name'])?></a>
				<?php
			}
			?>
		</h2>
		<?php
		if ($get_recent_posts && ! is_wp_error($get_recent_posts)) {
			?>
			<table class="display recent-posts-table">
				<thead>
				<tr>
					<th scope="col" class="column-primary">Title</th>
					<th id="action" scope="col">Action</th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ($get_recent_posts as $key => $value) {
					?>
					<tr>
						<td class="column-primary" data-colname="Title">
							<?php echo esc_html($value['title']['rendered']); ?>
						</td>

						<td data-colname="Action">
							<a class="add-to-posts" data-id="<?php echo esc_html($value['id']); ?>" href="<?php echo $admin_url;?>?add-to-post=<?php echo esc_html($value['id']); ?>">Add</a>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			<?php
		} else {
			// Show error if unable to display or fetch data.
			?>
			<div class="wp-ui-notification" id="error">
				<p><span class="dashicons dashicons-dismiss"></span> Unable to connect to API try reloading the page</p>
			</div>
			<p>
				<?php
				if (is_wp_error($get_recent_posts)) {
					$error_string = $get_recent_posts->get_error_message();
					echo '<p class="wp-ui-text-notification">' . esc_html($error_string) . '</p>';
				} ?>
			</p>
			<?php

		}
		?>
		<?php
	}

	public function add_new_post() {

	}
}
