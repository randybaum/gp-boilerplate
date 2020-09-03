<?php
/**
 * GeneratePress child theme functions and definitions.
 *
 * Add your custom PHP in this file. 
 * Only edit this file if you have direct access to it on your server (to fix errors if they happen).
 */

function generatepress_child_enqueue_scripts() {
	if ( is_rtl() ) {
		wp_enqueue_style( 'generatepress-rtl', trailingslashit( get_template_directory_uri() ) . 'rtl.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'generatepress_child_enqueue_scripts', 100 );


/**
 * Disable comments globally.
 */
add_action('admin_init', function () {
	// Redirect any user trying to access comments page
	global $pagenow;
	
	if ($pagenow === 'edit-comments.php') {
			wp_redirect(admin_url());
			exit;
	}
	// Remove comments metabox from dashboard
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
	// Disable support for comments and trackbacks in post types
	foreach (get_post_types() as $post_type) {
			if (post_type_supports($post_type, 'comments')) {
					remove_post_type_support($post_type, 'comments');
					remove_post_type_support($post_type, 'trackbacks');
			}
	}
});
// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);
// Remove comments page in menu
add_action('admin_menu', function () {
	remove_menu_page('edit-comments.php');
});
// Remove comments links from admin bar
add_action('init', function () {
	if (is_admin_bar_showing()) {
			remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
	}
});


/**
* Change wp-login logo URL
*/
function rb_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'rb_login_logo_url' );


/**
* Add custom styles to WP admin
*/
add_action('admin_head', 'rb_admin_styles');

function rb_admin_styles() {
echo '<style>
	/* REMOVE ADMIN MENU SEPARATORS & DASHBOARD WIDGETS */
	#adminmenu li.wp-menu-separator,
	#dashboard-widgets-wrap,
	/* REMOVE WPFORMS BRANDING */
	#wpforms-header,
	#wpforms-flyout,
	.wpforms-admin-page #footer-left,
	#wpforms-builder .wpforms-toolbar .wpforms-left,
	#wpforms-builder .wpforms-panel-sidebar-section.upgrade-modal, #wpforms-builder .wpforms-panel-sidebar-section.education-modal,
	#wpforms-panel-fields .wpforms-add-fields-button.upgrade-modal, #wpforms-panel-fields .wpforms-add-fields-button.education-modal, #wpforms-panel-fields .wpforms-add-fields-button:disabled {
		display: none;
	}
	#toplevel_page_wpforms-overview ul li a span {
		color: rgba(240,245,250,.7) !important;
	}
	#toplevel_page_wpforms-overview ul li a:hover span,
	#toplevel_page_wpforms-overview ul li a:focus span {
    	color: #00b9eb !important;
	}
	#wpforms-builder .wpforms-toolbar {
		text-align: left;
		padding-left: 1.5em;
	}
	</style>';
}


/**
* Remove unnecessary dashboard items.
*/
// Remove dashboard widgets
add_action('wp_dashboard_setup', 'rb_remove_all_dashboard_meta_boxes', 9999 );
function rb_remove_all_dashboard_meta_boxes()
{
	global $wp_meta_boxes;
	$wp_meta_boxes['dashboard']['normal']['core'] = array();
	$wp_meta_boxes['dashboard']['side']['core'] = array();
}
// Remove help tab
add_action( 'admin_head', 'rb_remove_wphelp_tab' );
function rb_remove_wphelp_tab()
{
	$screen = get_current_screen();
	$screen->remove_help_tabs();
}
// Remove welcome panel
remove_action('welcome_panel', 'wp_welcome_panel');
// Remove WP logo
 add_action( 'admin_bar_menu', 'rb_remove_wp_logo', 999 );
 function rb_remove_wp_logo( $wp_admin_bar ) {
 $wp_admin_bar->remove_node( 'wp-logo' );
}
// Remove unwanted admin bar links
function rb_remove_admin_bar_links() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('updates'); // Remove the updates link
	$wp_admin_bar->remove_menu('comments'); // Remove the comments link
	$wp_admin_bar->remove_menu('search'); // Remove the search link
}
add_action( 'wp_before_admin_bar_render', 'rb_remove_admin_bar_links' );


/**
* Load custom font in customizer
*/
add_filter( 'generate_typography_default_fonts', function( $fonts ) {
    $fonts[] = '';

    return $fonts;
} );


/**
* Load custom fonts for Gutenberg editor
*/
add_action( 'enqueue_block_editor_assets', function() {
    wp_enqueue_style( 'typekit-fonts', '#' );
} );


/**
* Load stylesheet for Gutenberg editor
*/
function rb_gutenberg_editor_styles() {
    wp_enqueue_style(
        'gutenberg-styles',
        get_stylesheet_directory_uri() . "/gutenberg-styles.css",
    );
}
add_action('enqueue_block_editor_assets', 'rb_gutenberg_editor_styles');


/**
 * Add "Reusable Blocks" to admin menu
 */
function rb_reusable_blocks_admin_menu() {
    add_menu_page( 'Reusable Blocks', 'Reusable Blocks', 'edit_posts', 'edit.php?post_type=wp_block', '', 'dashicons-update', 2 );
}
add_action( 'admin_menu', 'rb_reusable_blocks_admin_menu' );


/**
* Add inline svg logo
*/
add_filter( 'generate_logo_output', function( $output ) {
	printf(
		'<div class="site-logo">
			<a href="%1$s" title="%2$s" rel="home">
				<svg xmlns="http://www.w3.org/2000/svg" width="350" height="70" viewBox="0 0 350 70" overflow="visible"><path d="M0 69.9V0h17.2v54.8h19.9v15.1H0zm70.1-33.4c0-20.8 15-36.5 35.5-36.5 19.7 0 35.6 15.2 35.6 34.5 0 19.9-15.5 35.4-34.8 35.4-18.7 0-36.3-13.5-36.3-33.4zM124 35c0-10.6-7.6-19.9-18.4-19.9-10.7 0-18.3 9-18.3 19.7 0 11.1 7.6 20 18.6 20 10.6 0 18.1-9 18.1-19.8zm121.9-.9c0 18.1-13.3 35.9-36 35.9-19.9 0-35.8-15.6-35.8-35.3 0-20.4 16.5-34.7 36-34.7 15.2 0 29.9 10.3 33.7 23.8h-19.3c-3.2-5.6-8.4-8.7-14.8-8.7-10.8 0-18.4 8.7-18.4 19.6 0 11.2 7.1 20.2 18.5 20.2 8 0 14-3.9 16.1-10.8h-20.1V30.3h40.1v3.8zm33 2.4c0-20.8 15-36.5 35.5-36.5C334.1 0 350 15.2 350 34.5c0 19.9-15.5 35.4-34.8 35.4-18.7 0-36.3-13.5-36.3-33.4zm53.9-1.5c0-10.6-7.6-19.9-18.4-19.9-10.7 0-18.3 9-18.3 19.7 0 11.1 7.6 20 18.6 20 10.6 0 18.1-9 18.1-19.8z"/></svg>
			</a>
		</div>',
		esc_url( apply_filters( 'generate_logo_href' , home_url( '/' ) ) ),
		esc_attr( apply_filters( 'generate_logo_title', get_bloginfo( 'name', 'display' ) ) )
	);
} );


/**
* Add inline svg logo for sticky navigtion
*/
add_filter( 'generate_sticky_navigation_logo_output', function() {
    return sprintf(
        '<a href="%1$s" title="%2$s" rel="home">
			 <svg xmlns="http://www.w3.org/2000/svg" width="350" height="70" viewBox="0 0 350 70" overflow="visible"><path d="M0 69.9V0h17.2v54.8h19.9v15.1H0zm70.1-33.4c0-20.8 15-36.5 35.5-36.5 19.7 0 35.6 15.2 35.6 34.5 0 19.9-15.5 35.4-34.8 35.4-18.7 0-36.3-13.5-36.3-33.4zM124 35c0-10.6-7.6-19.9-18.4-19.9-10.7 0-18.3 9-18.3 19.7 0 11.1 7.6 20 18.6 20 10.6 0 18.1-9 18.1-19.8zm121.9-.9c0 18.1-13.3 35.9-36 35.9-19.9 0-35.8-15.6-35.8-35.3 0-20.4 16.5-34.7 36-34.7 15.2 0 29.9 10.3 33.7 23.8h-19.3c-3.2-5.6-8.4-8.7-14.8-8.7-10.8 0-18.4 8.7-18.4 19.6 0 11.2 7.1 20.2 18.5 20.2 8 0 14-3.9 16.1-10.8h-20.1V30.3h40.1v3.8zm33 2.4c0-20.8 15-36.5 35.5-36.5C334.1 0 350 15.2 350 34.5c0 19.9-15.5 35.4-34.8 35.4-18.7 0-36.3-13.5-36.3-33.4zm53.9-1.5c0-10.6-7.6-19.9-18.4-19.9-10.7 0-18.3 9-18.3 19.7 0 11.1 7.6 20 18.6 20 10.6 0 18.1-9 18.1-19.8z"/></svg>
		</a>',
		esc_url( apply_filters( 'generate_logo_href' , home_url( '/' ) ) ),
		esc_attr( apply_filters( 'generate_logo_title', get_bloginfo( 'name', 'display' ) ) ),
		esc_url( $settings['sticky_navigation_logo'] )
    );
} );


/**
* Use inline svg for mobile header logo
*/
add_filter( 'generate_mobile_header_logo_output', function( $output ) {
    if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
        return $output;
    }

    $settings = wp_parse_args(
        get_option( 'generate_menu_plus_settings', array() ),
        generate_menu_plus_get_defaults()
    );

    return sprintf(
        '<div class="site-logo mobile-header-logo">
            <a href="%1$s" title="%2$s" rel="home">
                <svg xmlns="http://www.w3.org/2000/svg" width="350" height="70" viewBox="0 0 350 70" overflow="visible"><path d="M0 69.9V0h17.2v54.8h19.9v15.1H0zm70.1-33.4c0-20.8 15-36.5 35.5-36.5 19.7 0 35.6 15.2 35.6 34.5 0 19.9-15.5 35.4-34.8 35.4-18.7 0-36.3-13.5-36.3-33.4zM124 35c0-10.6-7.6-19.9-18.4-19.9-10.7 0-18.3 9-18.3 19.7 0 11.1 7.6 20 18.6 20 10.6 0 18.1-9 18.1-19.8zm121.9-.9c0 18.1-13.3 35.9-36 35.9-19.9 0-35.8-15.6-35.8-35.3 0-20.4 16.5-34.7 36-34.7 15.2 0 29.9 10.3 33.7 23.8h-19.3c-3.2-5.6-8.4-8.7-14.8-8.7-10.8 0-18.4 8.7-18.4 19.6 0 11.2 7.1 20.2 18.5 20.2 8 0 14-3.9 16.1-10.8h-20.1V30.3h40.1v3.8zm33 2.4c0-20.8 15-36.5 35.5-36.5C334.1 0 350 15.2 350 34.5c0 19.9-15.5 35.4-34.8 35.4-18.7 0-36.3-13.5-36.3-33.4zm53.9-1.5c0-10.6-7.6-19.9-18.4-19.9-10.7 0-18.3 9-18.3 19.7 0 11.1 7.6 20 18.6 20 10.6 0 18.1-9 18.1-19.8z"/></svg>
            </a>
        </div>',
        esc_url( apply_filters( 'generate_logo_href' , home_url( '/' ) ) ),
        esc_attr( apply_filters( 'generate_logo_title', get_bloginfo( 'name', 'display' ) ) ),
        esc_url( apply_filters( 'generate_mobile_header_logo', $settings['mobile_header_logo'] ) ),
        esc_attr( apply_filters( 'generate_logo_title', get_bloginfo( 'name', 'display' ) ) )
    );
} );


/**
* Create shortcode to display site title
*/
function rb_site_title_shortcode( ){
   return get_bloginfo();
}
add_shortcode( 'site-title', 'rb_site_title_shortcode' );


/**
* Remove page titles globally
*/
add_action( 'after_setup_theme', 'rb_remove_all_titles' );
function rb_remove_all_titles() {
    add_filter( 'generate_show_title', '__return_false' );
}


/**
* Hide count from FacetWP dropdown fields
*/
add_filter( 'facetwp_facet_dropdown_show_counts', '__return_false' );


/**
* Disable GP pagination to allow FacetWP pagination
*/
add_action( 'wp', function() {
	add_filter( 'generate_show_post_navigation', '__return_false' );
} );


/**
* WPForms admin tweaks
*/
function rb_rename_wpforms_admin() {
    global $menu;

    // Define your changes here
    $updates = array(
        "WPForms" => array(
            'name' => 'Forms',
            'icon' => 'dashicons-email'
        )
    );

    foreach ( $menu as $k => $props ) {

        // Check for new values
        $new_values = ( isset( $updates[ $props[0] ] ) ) ? $updates[ $props[0] ] : false;
        if ( ! $new_values ) continue;

        // Change menu name
        $menu[$k][0] = $new_values['name'];

        // Optionally change menu icon
        if ( isset( $new_values['icon'] ) )
            $menu[$k][6] = $new_values['icon'];
    }
}
add_action( 'admin_init', 'rb_rename_wpforms_admin' );

// Hide WPForms menu items
function rb_hide_wpforms_menu_items() {
	remove_submenu_page('wpforms-overview', 'wpforms-addons');
	remove_submenu_page('wpforms-overview', 'wpforms-analytics');
	remove_submenu_page('wpforms-overview', 'wpforms-smtp');
	remove_submenu_page('wpforms-overview', 'wpforms-about');
	remove_submenu_page('wpforms-overview', 'wpforms-community');
}

add_action('admin_menu', 'rb_hide_wpforms_menu_items');
