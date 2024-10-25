<?php

// function add_developer_capability() {
//   /* Create a new capability for administratorsto control theme modules */
//   $role = get_role( 'administrator' );

//     // This only works, because it accesses the class instance.
//     // would allow the author to edit others' posts for current theme only
//     $role->add_cap( 'edit_modules_layout' );
//   /* set the first user (admin) to this role */
//   wp_update_user( array( 'ID' => 1, 'role' => 'administrator' ) );
// }
// add_action( 'init', 'developer_role' );

// function is_developer() {
//   if ( current_user_can('developer') ) {
//     return true;
//   }
//   return false;
// }

function balance_theme_setup() {

	add_theme_support( 'post-thumbnails' );

	// add Balance user types for specific products
	add_role('cu_partner', __( 'Credit Union/Partner', 'balance_theme' ), array(
		'read' 										=> true,
		'buy_cu_partner_products' => true,
	) );
	add_role('normal_cu', __( 'Normal CU User', 'balance_theme' ), array(
		'read' 						=> true,
		'buy_normal_cu_products' => true,
	) );
	add_role('balance', __( 'Normal Balance User', 'balance_theme' ), array(
		'read' 											=> true,
		'buy_balance_products' => true,
	) );
	$role = get_role('administrator');
	$role->add_cap('buy_cu_partner_products');
	$role->add_cap('buy_normal_cu_products');
	$role->add_cap('buy_balance_products');

	function remove_editor_and_thumbnails() {
		remove_post_type_support( 'page', 'editor' );
		remove_post_type_support( 'page', 'comments' );
		remove_post_type_support( 'post', 'editor' );
		remove_post_type_support( 'page', 'thumbnail' );
	}

	add_theme_support( 'woocommerce' );

	add_action( 'admin_init', 'remove_editor_and_thumbnails' );
}
add_action( 'after_setup_theme', 'balance_theme_setup' );
remove_action( 'wp_head', 'wp_shortlink_wp_head');

/**
 * Load CSS styles, JavaScript and jQuery files for theme.
 */
function balance_theme_styles_and_scripts_loader() {

	// if the includes should be minified or not (MINIFY_RESOURCES should be set through wp-config.php)
	$minify_path = '';
	if ( defined( 'MINIFY_RESOURCES' ) &&  MINIFY_RESOURCES ) {
		$minify_path = '.min';
	}
	// to be able to quickly reset caching we append number to the end of include (THEME_VERSION should be set through wp-config.php)
	if ( !defined( 'THEME_VERSION' ) ) {
		define( 'THEME_VERSION', '0.1' );
	}
	// added by megha
	if ( !defined( 'CUSTOMJS_VERSION' ) ) {
		define( 'CUSTOMJS_VERSION', '10.6');
	}
	// added by megha
	if ( !defined( 'LIFESTAGE_VERSION' ) ) {
		define( 'LIFESTAGE_VERSION', '10.6');
	}
	
	if(!defined('LIFESTAGE_PATH')) {
		define('LIFESTAGE_PATH', get_template_directory_uri());
	}

       
        if ( !defined( 'THEME_VERSION_CSS_NEW' ) ) {
                define( 'THEME_VERSION_CSS_NEW', '10.3' );
        }

	//wp_dequeue_script( 'jquery' );
	wp_dequeue_script( 'jquery-ui' );
	wp_dequeue_script( 'jquery-migrate' );
	if ( current_user_can( 'edit_pages' ) ) {
		wp_enqueue_style( 'dashicons' );
	}

	wp_enqueue_style( 'declaration-styles', get_stylesheet_directory_uri() . '/style.css', false, THEME_VERSION, 'all' );
	wp_enqueue_style( 'main-styles', get_stylesheet_directory_uri() . '/css/main.min_new.css', false, THEME_VERSION_CSS_NEW, 'all' );

	wp_enqueue_script( 'vendor-js', get_template_directory_uri() . '/js/vendor.js', array( 'jquery' ), THEME_VERSION, true );
	if ( basename( get_page_template() ) == 'template-T03-resources-landing.php' ) {
	wp_enqueue_script( 'custom-js', get_template_directory_uri() . '/js/custom-resource.js', array( 'jquery' ), CUSTOMJS_VERSION, true );
	//wp_enqueue_script( 'custom-js', get_template_directory_uri() . '/js/custom.js'.basename( get_page_template() ), array( 'jquery' ), CUSTOMJS_VERSION, true );
} else {
		wp_enqueue_script( 'custom-js-1', get_template_directory_uri() . '/js/custom-lifestage.js', array( 'jquery' ), LIFESTAGE_VERSION, true );

}
	wp_enqueue_script( 'plugins-js', get_template_directory_uri() . '/js/plugins.js', array( 'jquery' ), THEME_VERSION, true );
	wp_enqueue_script( 'modernizr-js', get_template_directory_uri() . '/js/vendor/modernizr.js', array( 'jquery' ), THEME_VERSION, true );

	wp_register_script( 'main-js', get_template_directory_uri() . '/js/main.js', array( 'jquery' ), THEME_VERSION, true );
	wp_localize_script( 'main-js', 'url_path', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'images_url' => get_template_directory_uri() . '/images/', 'home_url' => get_protocol_relative_home_url( true ) ) );
	//wp_localize_script( 'main-js', 'url_path', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'images_url' => get_template_directory_uri() . '/images/') );
	wp_localize_script( 'main-js', 'home_url', get_protocol_relative_home_url( true ) );
	wp_enqueue_script( 'main-js' );

	if ( basename( get_page_template() ) == 'template-T03-resources-landing.php' || is_singular( 'life_stage' ) ) {
		wp_register_script( 'resources-search-loader-js', get_template_directory_uri() . '/js/search.js', array( 'jquery' ), THEME_VERSION, true );
		wp_localize_script( 'resources-search-loader-js', 'paths',
			array(
				'searchResourcesBaseUrl' => admin_url( 'admin-ajax.php' ) . '?action=load_resources&query=',
				'getCategoriesUrl' => admin_url( 'admin-ajax.php' ) . '?action=load_resources&data=categories'
			)
		);
		wp_enqueue_script( 'resources-search-loader-js' );
	}

	if ( is_singular( 'calculator' ) || is_singular( 'quiz' ) ) {
		wp_register_script( 'calculator-iframe-resizer-content-window', get_template_directory_uri() . '/js/iframeResizer.contentWindow.min.js', array( 'jquery' ), THEME_VERSION, true );
		wp_enqueue_script( 'calculator-iframe-resizer-content-window' );
	}

}
add_action( 'wp_enqueue_scripts', 'balance_theme_styles_and_scripts_loader' );

/**
 * Load CSS styles, JavaScript and jQuery files for administration.
 */
function balance_theme_admin_styles_and_scripts_loader( $hook_suffix ) {
	// JS
	global $post_type;

	// to be able to quickly reset caching we append number to the end of include (THEME_VERSION should be set through wp-config.php)
	if ( !defined( 'THEME_VERSION' ) ) {
		define( 'THEME_VERSION', '0.1' );
	}

	// /home/janez/www/balance/balance-website/wp-includes/script-loader.php

	wp_dequeue_script( 'wplink' );
	wp_deregister_script( 'wplink' );
	wp_enqueue_script( 'wplink', get_template_directory_uri() . '/js/admin/wplink-custom.js', array(), THEME_VERSION, true );
	wp_localize_script( 'wplink', 'wpLinkL10n', array(
			'title' => __( 'Insert/edit link' ),
			'update' => __( 'Update' ),
			'save' => __( 'Add Link' ),
			'noTitle' => __( '(no title)' ),
			'noMatchesFound' => __( 'No results found.' )
		) );

	wp_dequeue_script( 'quicktags' );
	wp_deregister_script( 'quicktags' );
	wp_enqueue_script( 'quicktags', get_template_directory_uri() . '/js/admin/quicktags-custom.js', array(), THEME_VERSION, true );
	wp_localize_script( 'quicktags', 'quicktagsL10n', array(
			'closeAllOpenTags'      => __( 'Close all open tags' ),
			'closeTags'             => __( 'close tags' ),
			'enterURL'              => __( 'Enter the URL' ),
			'enterImageURL'         => __( 'Enter the URL of the image' ),
			'enterImageDescription' => __( 'Enter a description of the image' ),
			'textdirection'         => __( 'text direction' ),
			'toggleTextdirection'   => __( 'Toggle Editor Text Direction' ),
			'dfw'                   => __( 'Distraction-free writing mode' ),
			'strong'          => __( 'Bold' ),
			'strongClose'     => __( 'Close bold tag' ),
			'button'          => __( 'Button' ),
			'buttonClose'     => __( 'Close button' ),
			'em'              => __( 'Italic' ),
			'emClose'         => __( 'Close italic tag' ),
			'link'            => __( 'Insert link' ),
			'blockquote'      => __( 'Blockquote' ),
			'blockquoteClose' => __( 'Close blockquote tag' ),
			'del'             => __( 'Deleted text (strikethrough)' ),
			'delClose'        => __( 'Close deleted text tag' ),
			'ins'             => __( 'Inserted text' ),
			'insClose'        => __( 'Close inserted text tag' ),
			'image'           => __( 'Insert image' ),
			'ul'              => __( 'Bulleted list' ),
			'ulClose'         => __( 'Close bulleted list tag' ),
			'ol'              => __( 'Numbered list' ),
			'olClose'         => __( 'Close numbered list tag' ),
			'li'              => __( 'List item' ),
			'liClose'         => __( 'Close list item tag' ),
			'code'            => __( 'Code' ),
			'codeClose'       => __( 'Close code tag' ),
			'more'            => __( 'Insert Read More tag' ),
		) );

	//Check if is post_type or setting page
	if ( ( isset( $post_type ) && !empty( $post_type ) ) || ( !empty( $hook_suffix ) && substr( $hook_suffix, 0, 13 ) === 'settings_page' ) || ( !empty( $hook_suffix ) && substr( $hook_suffix, 0, 25 ) === 'toplevel_page_nestedpages' ) || ( !empty( $hook_suffix ) && substr( $hook_suffix, 0, 9 ) === 'resources' ) ) {
		wp_enqueue_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js', array(), THEME_VERSION, true );
		wp_enqueue_script( 'jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js', array( 'jquery' ), THEME_VERSION, true );

		wp_enqueue_script( 'tokenize-custom-js', get_template_directory_uri() . '/js/admin/jquery.tokenize.custom.js', array( 'jquery' ), THEME_VERSION, true );
		wp_enqueue_script( 'admin-js', get_template_directory_uri() . '/js/admin/admin.js', array( 'jquery' ), THEME_VERSION, true );
		wp_localize_script( 'admin-js', 'url_path', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'images_url' => get_template_directory_uri() . '/images/', 'home_url' => get_protocol_relative_home_url( true ) ) );
		wp_localize_script( 'admin-js', 'theme', array(
				'template_directory_uri' => get_template_directory_uri(),
				'choose' => __( 'Upload or select from library' , 'balance_theme' ),
				'upload_select' => __( 'Upload / Select', 'balance_theme' ),
				'change' => __( 'Change', 'balance_theme' ),
				'select' => __( 'Select' , 'dnd-shortcodes' ),
				'confirm_delete' => __( 'Are you sure?', 'balance_theme' ),
				'min_one_slide_error' => __( 'There needs to be at least one!', 'balance_theme' ),
				'move' => __( 'Drag up or down & drop into placeholder to reorder items', 'balance_theme' ),
				'includes_url' => includes_url()
			) );
		js_wp_editor();
		// CSS styles
		wp_enqueue_style( 'jquery-ui-styles', get_template_directory_uri() . '/css/vendor/jquery-ui.min.css', false, THEME_VERSION, 'all' );
		wp_enqueue_style( 'tokenize-custom-styles', get_template_directory_uri() . '/css/admin/jquery.tokenize.custom.css', false, THEME_VERSION, 'all' );
	}
	if ( is_admin() ) {
		wp_enqueue_style( 'admin-styles', get_template_directory_uri() . '/css/admin/admin.css', false, THEME_VERSION, 'all' );
	}
}
add_action( 'admin_enqueue_scripts', 'balance_theme_admin_styles_and_scripts_loader' );

// add Admin CSS styles to dashboard
add_action( 'admin_head-index.php', 'dashboard_styles' );
function dashboard_styles() {
	// to be able to quickly reset caching we append number to the end of include (THEME_VERSION should be set through wp-config.php)
	if ( !defined( 'THEME_VERSION' ) ) {
		define( 'THEME_VERSION', '0.1' );
	}
	wp_enqueue_style( 'admin-styles', get_template_directory_uri() . '/css/admin/admin.css', false, THEME_VERSION, 'all' );
}

/**
 * Custom functions:
 */
require_once get_template_directory() . '/inc/inc.php';

// remove unnecessary header info
function remove_header_info() {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'start_post_rel_link' );
	remove_action( 'wp_head', 'index_rel_link' );
	remove_action( 'wp_head', 'adjacent_posts_rel_link' );         // for WordPress <  3.0
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' ); // for WordPress >= 3.0
}
add_action( 'init', 'remove_header_info' );

add_filter( 'wp_title', 'hack_wp_title_for_home' );
function hack_wp_title_for_home( $title ) {
	if ( empty( $title ) && ( is_home() || is_front_page() ) ) {
		return ' | ' . __( 'Welcome', 'balance_theme' );
	}
	return $title;
}

function remove_admin_bar_links() {
	global $wp_admin_bar;
	// $wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
	$wp_admin_bar->remove_menu( 'about' );            // Remove the about WordPress link
	$wp_admin_bar->remove_menu( 'wporg' );            // Remove the WordPress.org link
	$wp_admin_bar->remove_menu( 'documentation' );    // Remove the WordPress documentation link
	// $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
	// $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
	$wp_admin_bar->remove_menu( 'updates' );          // Remove the updates link
	$wp_admin_bar->remove_menu( 'comments' );         // Remove the comments link
	// $wp_admin_bar->remove_menu('new-content');      // Remove the content link
	// $wp_admin_bar->remove_menu('w3tc');             // If you use w3 total cache remove the performance link
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

// remove extra CSS that 'Recent Comments' widget injects
function remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array(
			$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
			'recent_comments_style'
		)
	);
}
add_action( 'widgets_init', 'remove_recent_comments_style' );

/*
* Hide unused functionalities in administration
*/
add_action( 'admin_menu', 'balance_theme_remove_menu_pages' );
function balance_theme_remove_menu_pages() {
	if ( ! is_admin() )
		return false;
	global $menu;
	remove_menu_page( 'link-manager.php' ); // link are not in use
	remove_menu_page( 'edit-comments.php' );  // comments functionality is not in use
	remove_menu_page( 'themes.php' );
	remove_submenu_page( 'themes.php', 'nav-menus.php' );
	remove_submenu_page( 'themes.php', 'customize.php' );
	// add_menu_page( __( 'Themes', 'balance_theme' ), __( 'Themes', 'balance_theme' ), 'manage_options', 'themes.php', '', 'dashicons-admin-appearance', 62 );
	add_menu_page( __( 'Menus', 'balance_theme' ), __( 'Menus', 'balance_theme' ), 'manage_options', 'nav-menus.php', '', 'dashicons-welcome-widgets-menus', 42 );
	$menu[52]=$menu[10];// position lower (media)
	unset( $menu[10] );// make original node disappear (media)
}

/*
* Hides the administration menu on website for logged in users
*/
add_filter( 'show_admin_bar', '__return_false' );
/*
* Prevents splitting uploads folder (for media) in year/month format --> "one folder holds all"
*/
add_filter( 'option_uploads_use_yearmonth_folders', '__return_false', 100 );

// WPNOTE: PRETTY PRINT_R
function print_rr( $data, $end = false ) {
	echo "<pre>";
	print_r( $data );
	if ( $end ) {
		die( "</pre>" );
	} else {
		echo "</pre>";
	}
}
// WPNOTE: PRETTY VAR_DUMP
function var_dumpp( $data, $end = false ) {
	echo "<pre>";
	var_dump( $data );
	if ( $end ) {
		die( "</pre>" );
	} else {
		echo "</pre>";
	}
}

function get_children_by_pageID( $pageID ) {
	$args = array(
		'child_of' => $pageID,
		'parent' => $pageID,
		'post_type' => 'page'
	);
	$children = get_pages( $args );
	return $children;
}

function isPhone() {
	require_once get_template_directory() . '/inc/Mobile_Detect.php';
	$isphone = false;
	$detect = new Mobile_Detect;
	if ( $detect->isMobile() && !$detect->isTablet() ) {
		$isphone = true;
	}
	return $isphone;
}

function isTouchDevice() {
	require_once get_template_directory() . '/inc/Mobile_Detect.php';
	$istouchdevice = false;
	$detect = new Mobile_Detect;
	if ( $detect->isMobile() || $detect->isTablet() ) {
		$istouchdevice = true;
	}
	return $istouchdevice;
}

function isTablet() {
	require_once get_template_directory() . '/inc/Mobile_Detect.php';
	$istablet = false;
	$detect = new Mobile_Detect;
	if ( $detect->isTablet() ) {
		$istablet = true;
	}
	return $istablet;
}

add_filter( 'body_class', 'browser_body_class', 10, 3 );
function browser_body_class( $classes ) {
	if ( is_home() ) {
		$classes[] = 'homepage';
	} else {
		$classes[] = 'db-page';
	}
	if ( isPhone() ) {
		$classes[] = 'mobile';
	}
	if ( isTouchDevice() ) {
		$classes[] = 'touch-device';
	}
	if ( isTablet() ) {
		$classes[] = 'tablet';
	}
	return $classes;
}

add_filter( 'body_class', 'slug_body_class', 10, 3 );
function slug_body_class( $classes ) {
	global $post;
	if ( empty( $post ) ) {
		return $classes;
	}
	$slug = get_post( $post )->post_name;
	$classes[] = $slug;
	return $classes;
}

remove_filter( 'nav_menu_description', 'strip_tags' );
add_filter( 'wp_setup_nav_menu_item', 'menu_item_desc_html_tags' );
function menu_item_desc_html_tags( $menu_item ) {
	$menu_item->description = apply_filters( 'nav_menu_description',  $menu_item->post_content );
	return $menu_item;
}

// create custom settings menu
add_action( 'admin_menu', 'scripts_create_menu' );

function scripts_create_menu() {
	// create new top-level menu
	add_menu_page( __( 'Scripts & Styles', 'balance_theme' ), __( 'Scripts & Styles', 'balance_theme' ), 'administrator', 'scripts-and-styles', 'scripts_settings_page', 'dashicons-media-code', 60 );
	// call register settings function
	add_action( 'admin_init', 'register_scripts_settings' );
}

function register_scripts_settings() {
	register_setting( 'scripts-settings', 'header_scripts' );
	register_setting( 'scripts-settings', 'footer_scripts' );
}

function scripts_settings_page() {
	echo '<div class="wrap">';
	echo '<h2>' . __( 'Scripts & Styles', 'balance_theme' ) . '</h2>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'scripts-settings' );
	do_settings_sections( 'scripts-settings' );
	echo '<style type="text/css">';
	echo 'i { display: inline-block; font-weight:normal; font-style: normal; }';
	echo 'pre { display: inline-block; background: #cecece; letter-spacing: -1px; font-size: 13px; margin-top: 10px; margin-bottom: 0; }';
	echo '.form-table th, .form-table td { padding: 10px 0 }';
	echo '</style>';
	echo '<table class="form-table">';
	echo '<tr valign="top">';
	echo '<th scope="row">';
	echo __( 'Header Scripts & styles', 'balance_theme' );
	echo '<br>';
	echo '<i>' . __( 'Add <pre>&lt;link /&gt;</pre>, <pre>&lt;meta /&gt;</pre>, <pre>&lt;style /&gt;</pre>, <pre>&lt;script /&gt;</pre>,... that should be added <b>before</b> <pre>&lt;/head&gt;</pre> tag (in the header)', 'balance_theme' ) . '</i>';
	echo '</th>';
	echo '</tr>';
	echo '<tr valign="top">';
	echo '<td scope="row"><textarea name="header_scripts" rows="9" cols="20" style="width:100%;">' . get_option( 'header_scripts' ) . '</textarea></td>';
	echo '</tr>';
	echo '<tr valign="top">';
	echo '<th scope="row">';
	echo __( 'Footer Scripts & styles', 'balance_theme' );
	echo '<br>';
	echo '<i>' . __( 'Add <pre>&lt;link /&gt;</pre>, <pre>&lt;style /&gt;</pre>, <pre>&lt;script /&gt;</pre>,... that should be added <b>before</b> <pre>&lt;/body&gt;</pre> tag (at the bottom of the body)', 'balance_theme' ) . '</i>';
	echo '</th>';
	echo '</tr>';
	echo '<tr valign="top">';
	echo '<td scope="row"><textarea name="footer_scripts" rows="9" cols="20" style="width:100%;">' . get_option( 'footer_scripts' ) . '</textarea></td>';
	echo '</tr>';
	echo '</table>';
	submit_button();
	echo '</form>';
	echo '</div>';
}

function add_opengraph_meta() {
	global $post;

	if ( is_singular() ) {
		if ( has_post_thumbnail( $post->ID ) ) {
			$img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
?>
         <meta property="og:image" content="<?php echo $img_src[0]; ?>"/>
         <?php
		} else {
			$img_from_content = catch_that_image( $post->post_content );
			if ( !empty( $img_from_content ) ) {
?>
       <meta property="og:image" content="<?php echo $img_from_content; ?>"/>
       <?php
			}
		}
		if ( $excerpt = $post->post_excerpt ) {
			$excerpt = strip_tags( $post->post_excerpt );
			$excerpt = str_replace( "", "'", $excerpt );
		} else {
			$excerpt = get_bloginfo( 'description' );
		}
?>
     <meta property="og:title" content="<?php echo the_title(); ?>"/>
     <meta property="og:description" content="<?php echo $excerpt; ?>"/>
     <meta property="og:type" content="article"/>
     <meta property="og:url" content="<?php echo get_permalink(); ?>"/>
     <meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/>
   <?php
	} else {
		return;
	}
}
add_action( 'wp_head', 'add_opengraph_meta', 5 );


function balance_remove_excerpt() {
	remove_post_type_support( 'post', 'excerpt' );
	remove_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'balance_remove_excerpt' );

//allow svg uploads
function cc_mime_types( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

add_filter( 'template_include', 'var_template_include', 1000 );
function var_template_include( $t ) {
	$GLOBALS['current_theme_template'] = basename( $t );
	return $t;
}


function get_current_template( $echo = false ) {
	if ( !isset( $GLOBALS['current_theme_template'] ) )
		return false;
	if ( $echo )
		echo $GLOBALS['current_theme_template'];
	else
		return $GLOBALS['current_theme_template'];
}

function news_rewrite_rule() {
	add_rewrite_rule( '/news/page/([0-9]{1,})/?$', 'index.php?&pagename=news&paged=$matches[1]', 'top' );
}
add_action( 'init', 'news_rewrite_rule' );

function programs_rewrite_rule() {
	add_rewrite_rule( '/programs/([^/]+)/?$', 'index.php?&program=$matches[1]&paged=1', 'top' );
	add_rewrite_rule( '/programs/([^/]+)/page/([0-9]{1,})/?$', 'index.php?&program=$matches[1]&paged=$matches[2]', 'top' );
}
add_action( 'init', 'programs_rewrite_rule', 1 );

//Remove original canonical check and replace it with
//our own which skips cannonical redirects for articles and programs
remove_filter( 'template_redirect', 'redirect_canonical' );
function custom_canonical_check( $requested_url = null, $do_redirect = true ) {
	global $wp_rewrite, $is_IIS, $wp_query, $wpdb, $wp;

	if ( ! $requested_url && isset( $_SERVER['HTTP_HOST'] ) ) {
		// build the URL in the address bar
		$requested_url  = is_ssl() ? 'https://' : 'http://';
		$requested_url .= $_SERVER['HTTP_HOST'];
		$requested_url .= $_SERVER['REQUEST_URI'];
	}

	$original = @parse_url( $requested_url );

	if ( preg_match( '/^\/programs\/([^\/]+)\/?/', $original['path'] , $match ) || preg_match( '/^\/resources\/articles\/([^\/]+)\/?/', $original['path'] ) ) {
		return;
	}else {
		redirect_canonical();
	}
}
add_filter( 'template_redirect', 'custom_canonical_check' );

add_filter( 'embed_oembed_html', 'balance_embed_oembed_html', 99, 4 );
function balance_embed_oembed_html( $html, $url, $attr, $post_id ) {
	return '<div class="video-holder">' . $html . '</div>';
}

/**
 * Disable the emoji's
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @param array   $plugins
 * @return   array             Difference betwen the two arrays
 */
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

add_action( 'init', 'define_frame_users' );
function define_frame_users() {
	if ( ! defined( 'FRAME_USERS' ) ) {
		define ( 'FRAME_USERS', 'normal_cu,cu_partner' );
	}
}

add_action( 'template_redirect', 'authenticate_with_token' );
function authenticate_with_token() {
	// check for access token and login user if provided
	global $wpdb;
	$ecomm_token = false;
	$token_table = "wlw_access_tokens";
	$uid         = $tkn_id = 0;

	/* debug variable
	global $tkn_log;
	$tkn_log = "";*/

	if ( ! empty( $_GET['tkn'] ) ) {
		$ecomm_token = $_GET['tkn'];
		//$tkn_log .= 'token provided.';

		if ( strlen( $ecomm_token ) > 0 ) {
			$temp_token = $wpdb->get_row( 'SELECT id, wp_user_id FROM ' . $token_table . ' WHERE token="' . $ecomm_token .'" AND created_at > NOW() - INTERVAL 2 MINUTE' );

			if ( $temp_token ) {

				//$tkn_log .= " token found in the db.";
				$uid       = $temp_token->wp_user_id;
				//$tkn_log .= " user ". $uid . ".";
				$tkn_id    = $temp_token->id;
				$user      = get_user_by( 'id', $uid );

				if ( false !== $user ) {
					wp_clear_auth_cookie();
				  	wp_set_current_user( $uid );
				  	wp_set_auth_cookie( $uid );
				}

				//if ( $wpdb->delete( $token_table, array( 'id' => $tkn_id ), array( '%d' ) ) !== false ) $tkn_log .= " token deleted.";
		    	#$wpdb->delete( $token_table, array( 'id' => $tkn_id ), array( '%d' ) );
			}
		}
	}
}

// display Product type field
add_action( 'woocommerce_product_options_general_product_data', 'woo_add_custom_general_fields' );

function woo_add_custom_general_fields() {
  global $woocommerce, $post;

	$product_type = get_post_meta( $post->ID, '_balance_product_type', true );
  echo '<div class="options_group">';
	$product_type = explode( ',', $product_type );
	woocommerce_wp_checkbox(
		array(
			'id'            => '_product_type_public',
			'wrapper_class' => 'inline',
			'label'         => __( 'Balance Product Type', 'balance_theme' ),
			'cbvalue'				=> 1,
			'description'   => __( 'Public Product', 'woocommerce' )
		)
	);
	woocommerce_wp_checkbox(
		array(
			'id'            => '_product_type_cu_partner',
			'wrapper_class' => 'inline  no-label',
			'label'					=> '',
			'cbvalue'				=> 1,
			'description'   => __( 'Credit Union/Partner product', 'woocommerce' )
		)
	);
	woocommerce_wp_checkbox(
		array(
			'id'            => '_product_type_normal_cu',
			'wrapper_class' => 'inline no-label',
			'label'					=> '',
			'cbvalue'				=> 1,
			'description'   => __( 'Normal CU User Product', 'woocommerce' )
		)
	);
	woocommerce_wp_checkbox(
		array(
			'id'            => '_product_type_balance',
			'wrapper_class' => 'inline no-label',
			'label'					=> '',
			'cbvalue'				=> 1,
			'description'   => __( 'Normal Balance User Product', 'woocommerce' )
		)
	);
  echo '</div>';
}

// save Balance product type fields
add_action( 'woocommerce_process_product_meta', 'woo_add_custom_general_fields_save' );

function woo_add_custom_general_fields_save( $post_id ) {
	$woo_balance_ptype_public = ! empty( $_POST['_product_type_public'] ) ? $_POST['_product_type_public'] : '';
	$woo_balance_ptype_partner = ! empty( $_POST['_product_type_cu_partner'] ) ? $_POST['_product_type_cu_partner'] : '';
	$woo_balance_ptype_cunion = ! empty( $_POST['_product_type_normal_cu'] ) ? $_POST['_product_type_normal_cu'] : '';
	$woo_balance_ptype_balance = ! empty( $_POST['_product_type_balance'] ) ? $_POST['_product_type_balance'] : '';
	update_post_meta( $post_id, '_product_type_public', $woo_balance_ptype_public );
	update_post_meta( $post_id, '_product_type_cu_partner', $woo_balance_ptype_partner );
	update_post_meta( $post_id, '_product_type_normal_cu', $woo_balance_ptype_cunion );
	update_post_meta( $post_id, '_product_type_balance', $woo_balance_ptype_balance );
}

// check if product type matches the user type
add_filter( 'woocommerce_add_to_cart_validation', 'balance_validate_add_cart_item', 10, 5 );

function balance_validate_add_cart_item( $passed, $product_id, $quantity, $variation_id = '', $variations= '' ) {
	if ( ! is_user_logged_in() && get_post_meta( $product_id, '_product_type_public', true) ) {
		return $passed;
	}

	if ( current_user_can( 'administrator' ) ) {
		return $passed;
	}

	$product_type = [];
	$ptype_partner = get_post_meta( $product_id, '_product_type_cu_partner', true);
	$ptype_cu = get_post_meta( $product_id, '_product_type_normal_cu', true);
	$ptype_balance = get_post_meta( $product_id, '_product_type_balance', true);

	if ( $ptype_partner ) $product_type[] = 'cu_partner';
	if ( $ptype_cu ) $product_type[] = 'normal_cu';
	if ( $ptype_balance ) $product_type[] = 'balance';

	$passed = true;

	if ( ! empty ( $product_type ) ) {
		$passed = false;
		foreach ( $product_type as $ptype ) {
			if ( current_user_can( 'buy_'.$ptype.'_products' ) ) {
				$passed = true;
				break;
			}
		}
		if ( ! $passed ) {
			wc_add_notice( __( 'You cannot buy this product', 'balance_theme' ), 'error' );
		}
	}

  return $passed;
}

// display only products that match the user type
add_action( 'woocommerce_product_query', 'filter_products_by_user_type' );

function filter_products_by_user_type( $q ) {
	// show public products to guest users and default user roles
	if ( ! is_user_logged_in() || ( is_user_logged_in() && empty( array_intersect( wp_get_current_user()->roles, ['balance', 'normal_cu', 'cu_partner'] ) ) ) ) {
		$meta_query = $q->get( 'meta_query' );
		$meta_query[] = array(
			$meta_query_args = array(
				array(
					'key' => '_product_type_public',
					'value' => '1',
					'compare' => '='
				)
			)
		);
	}
	// show all products to administrators
	else if ( current_user_can( 'administrator' ) ) {
		$meta_query = $q->get( 'meta_query' );
	}
	// show a limited collection of products to other users
	else {
		$product_types = [];
		if ( current_user_can( 'buy_cu_partner_products') ) {
			$product_types[] = array(
				'key' => '_product_type_cu_partner',
				'value' => '1',
				'compare' => '='
			);
		}
		if ( current_user_can( 'buy_normal_cu_products') ) {
			$product_types[] = array(
				'key' => '_product_type_normal_cu',
				'value' => '1',
				'compare' => '='
			);
		}
		if ( current_user_can( 'buy_balance_products') ) {
			$product_types[] = array(
				'key' => '_product_type_balance',
				'value' => '1',
				'compare' => '='
			);
		}

		$meta_query = $q->get( 'meta_query' );
	  $meta_query[] = array(
			$meta_query_args = array(
				'relation' => 'OR',
				$product_types
			)
		);
	}

  $q->set( 'meta_query', $meta_query );
}

function quiz_add_to_cart() {
	global $woocommerce;
	$in_cart = false;

  if ( isset( $_REQUEST ) && isset( $_REQUEST['quiz_id'] ) && isset( $_REQUEST['product_id'] ) ) {
		foreach( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];

			if( $_REQUEST['product_id'] == $_product->id ) {
				$in_cart = true;
			}
		}
		if ( ! $in_cart ) {
			if ( $woocommerce->cart->add_to_cart( $_REQUEST['product_id'] ) != false ) {
				$_SESSION['quiz_in_cart'] = $_REQUEST['quiz_id'];
				echo 1;
			}
		}
		else {
			echo 0;
		}
		die();
  }
	echo -1;
 	die();
}
add_action( 'wp_ajax_quiz_add_to_cart', 'quiz_add_to_cart' );
add_action( 'wp_ajax_nopriv_quiz_add_to_cart', 'quiz_add_to_cart' );

function output_js_ajaxurl() { ?>
	<script type="text/javascript">
	var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	</script>
<?php
}
add_action( 'wp_head', 'output_js_ajaxurl');

// remove quiz id from session when subscription is removed from cart and quiz id exists
function ss_cart_updated( $cart_item_key, $cart ) {
    $product_id = $cart->cart_contents[ $cart_item_key ]['product_id'];
		$product = get_product( $product_id );

		if ( $product->is_type( 'subscription' ) && ! empty( $_SESSION['quiz_in_cart'] ) ) {
			unset( $_SESSION['quiz_in_cart'] );
		}
};
add_action( 'woocommerce_remove_cart_item', 'ss_cart_updated', 10, 2 );

// return plain header to display in iframe
function getHeaderType() {
	$frame = explode(',', FRAME_USERS);
	if ( is_user_logged_in() && ! empty( array_intersect( wp_get_current_user()->roles, $frame ) ) ) {
		return 'plain';
	}
	return '';
}

// add custom settings
add_filter( 'gform_form_settings', 'summary_surplus_shortfall_message_setting', 10, 2 );
function summary_surplus_shortfall_message_setting( $settings, $form ) {
	if ( rgar( $form, 'title' ) == 'Pathways Budget' ) {
		if ( ! array_key_exists( $settings, 'Balance Pathways Budget' ) ) {
			$settings['Balance Pathways Budget'] = [];
		}
    $settings['Balance Pathways Budget']['summary_message_surplus'] = '
        <tr>
            <th><label for="summary_message_surplus">Custom surplus message</label></th>
            <td><textarea name="summary_message_surplus">' . rgar($form, 'summary_message_surplus') . '</textarea></td>
        </tr>';
		$settings['Balance Pathways Budget']['summary_message_shortfall'] = '
        <tr>
            <th><label for="summary_message_shortfall">Custom shortfall message</label></th>
            <td><textarea name="summary_message_shortfall">' . rgar($form, 'summary_message_shortfall') . '</textarea></td>
        </tr>';
		}
  return $settings;
}

// save your custom form setting
add_filter( 'gform_pre_form_settings_save', 'save_summary_surplus_shortfall_message_setting' );
function save_summary_surplus_shortfall_message_setting($form) {
	if ( rgar( $form, 'title' ) == 'Pathways Budget' ) {
    $form['summary_message_surplus'] = rgpost( 'summary_message_surplus' );
		$form['summary_message_shortfall'] = rgpost( 'summary_message_shortfall' );
	}
  return $form;
}

function delete_wlw_user_after_wp_deletion( $user_id ) {
	global $wpdb;

  $user = get_userdata( $user_id );
  $email = $user->user_email;

	$wpdb->delete(
		'whitelabel_users',
		array(
			'email' => $email,
		)
	);
}
add_action( 'delete_user', 'delete_wlw_user_after_wp_deletion' );

add_action( 'init' , 'remove_wc_frame_options_header' , 15 );
function remove_wc_frame_options_header() {
  remove_action( 'template_redirect', 'wc_send_frame_options_header', 10 );
}

// add_action('wp_ajax_save_custom_holidays', 'save_custom_holidays');
// function save_custom_holidays() {
//     return "AJAX is working!";
// }

function save_custom_holiday() {
    global $wpdb;
    $post_id = intval($_POST['post_id']);
    $holiday_name = sanitize_text_field($_POST['holiday_name']);
    $holiday_date = sanitize_text_field($_POST['holiday_date']);

    $year = date('Y'); // Get the current year or use a dynamic year as needed
    // Array of predefined holidays
    $holidays = array(
        'New Year\'s Day' => date('Y-m-d', strtotime("$year-01-01")),
        'Martin Luther King Birthday' => date('Y-m-d', strtotime("third Monday of January $year")),
        'Presidents\' Day' => date('Y-m-d', strtotime("third Monday of February $year")),
        'Memorial Day' => date('Y-m-d', strtotime("last Monday of May $year")),
        'Independence Day' => date('Y-m-d', strtotime("$year-07-04")),
        'Labor Day' => date('Y-m-d', strtotime("first Monday of September $year")),
        'Columbus Day' => date('Y-m-d', strtotime("second Monday of October $year")),
        'Veterans\' Day' => date('Y-m-d', strtotime("$year-11-11")),
        'Thanksgiving Day' => date('Y-m-d', strtotime("fourth Thursday of November $year")),
        'Christmas Day' => date('Y-m-d', strtotime("$year-12-25"))
    );

    // Check if the entered holiday exists in predefined holidays
    foreach ($holidays as $predefined_name => $predefined_date) {
        if ($holiday_name === $predefined_name && $holiday_date === $predefined_date) {
            // Predefined holiday exists, return an error response
            wp_send_json_error(['message' => 'This is a predefined holiday and already exists']);
            wp_die();
        }
    }

    $table_name = $wpdb->prefix . 'white_label_websites';
    $holidays_json = $wpdb->get_var( $wpdb->prepare( "SELECT custom_holidays FROM $table_name WHERE wp_post_id = %d", $post_id ) );
    $custom_holidays = json_decode( $holidays_json, true ) ?: [];

    // Check if the holiday already exists in custom holidays
    foreach ($custom_holidays as $holiday) {
        if ($holiday['name'] === $holiday_name && $holiday['date'] === $holiday_date) {
            // Custom holiday already exists, return an error response
            wp_send_json_error(['message' => 'Holiday already exists']);
            wp_die();
        }
    }

    // Add the new holiday if it doesn't exist
    $custom_holidays[] = [
        'name' => $holiday_name,
        'date' => $holiday_date,
    ];

    // Update the database
    $wpdb->update(
        $table_name,
        ['custom_holidays' => json_encode($custom_holidays)],
        ['wp_post_id' => $post_id]
    );

    // Return the newly added holiday HTML
    echo '<div class="holiday-item" data-index="'. (count($custom_holidays) - 1) .'">';
    echo '<span class="hlabels">Holiday name: </span><input type="text" value="'. esc_attr($holiday_name) .'" readonly/>';
    echo '<span class="hlabels"> On: </span><input type="date" value="'. esc_attr($holiday_date) .'" readonly/>';
    echo '<button class="red-btn remove-holiday">Remove</button>';
    echo '</div><br>';
    wp_die();
}

add_action('wp_ajax_save_custom_holiday', 'save_custom_holiday');
add_action('wp_ajax_nopriv_save_custom_holiday', 'save_custom_holiday');

function old_save_custom_holidays() {
    global $wpdb;

	$holidays=$_POST['holidays'];
	
    if (isset($_POST['post_id']) && isset($holidays) && $holidays!='' && $holidays!=null) {
        $post_id = intval($_POST['post_id']);
        $holidays = wp_unslash($_POST['holidays']);
        
        error_log("Post ID: " . $post_id);
        error_log("Holidays Data: " . $holidays);

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        if (!current_user_can('edit_page', $post_id)) return;

        $holidays_decoded = json_decode($holidays, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $table_name = $wpdb->prefix . 'white_label_websites';
            $updated = $wpdb->update(
                $table_name,
                array('custom_holidays' => maybe_serialize($holidays)), // Serialize for safe storage
                array('wp_post_id' => $post_id),
                array('%s'),
                array('%d')
            );

            if ($updated) {
                error_log("Holidays updated successfully for post_id: $post_id.");
                wp_send_json_success("Holidays updated!");
            } else {
                error_log("Failed to update holidays.");
                wp_send_json_error("Failed to update holidays.");
            }
        } else {
            error_log("Invalid JSON data.");
            wp_send_json_error("Invalid JSON data.");
        }
    } else {
        error_log("Missing post_id or holidays in the request.");
        wp_send_json_error("Missing data.");
    }
}

add_action('wp_ajax_remove_custom_holiday', 'remove_custom_holiday');

function remove_custom_holidays() {
    // Get the POST data
    $holiday_name = isset($_POST['holiday_name']) ? sanitize_text_field($_POST['holiday_name']) : '';
    $holiday_date = isset($_POST['holiday_date']) ? sanitize_text_field($_POST['holiday_date']) : '';
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
	
    if (empty($holiday_name) || empty($holiday_date)) {
        wp_send_json_error('Holiday name or date missing.');
        return;
    }

    // Retrieve the current custom holidays (assuming they are saved in post meta or an options table)
    $custom_holidays = get_post_meta($post_id, 'custom_holidays', true);

    // Remove the holiday from the array
    $updated_holidays = array_filter($custom_holidays, function($holiday) use ($holiday_name, $holiday_date) {
        return !($holiday['name'] === $holiday_name && $holiday['date'] === $holiday_date);
    });

    // Update the post meta or option after removal
    update_post_meta($post_id, 'custom_holidays', $updated_holidays);

    wp_send_json_success('Holiday removed.');
}
function remove_custom_holiday() {
    global $wpdb;
    $post_id = intval($_POST['post_id']);
    $index = intval($_POST['index']);

    $table_name = $wpdb->prefix . 'white_label_websites';
    $holidays_json = $wpdb->get_var( $wpdb->prepare( "SELECT custom_holidays FROM $table_name WHERE wp_post_id = %d", $post_id ) );
    $custom_holidays = json_decode( $holidays_json, true ) ?: [];

    // Remove the holiday by index
    if (isset($custom_holidays[$index])) {
        unset($custom_holidays[$index]);
        $custom_holidays = array_values($custom_holidays); // Reindex the array
    }

    // Update the database
    $wpdb->update(
        $table_name,
        ['custom_holidays' => json_encode($custom_holidays)],
        ['wp_post_id' => $post_id]
    );

    wp_send_json_success();
}

/**
 * Change all WordPress URLs to relative to account for Partner sites
 * 
 * @type Array
 */
$filters = array( 
	'post_link',
	'post_type_link',
	'page_link',
	'attachment_link',
	'get_shortlink',
	'post_type_archive_link',
	'get_pagenum_link',
	'get_comments_pagenum_link',
	'term_link',
	'search_link',
	'day_link',
	'month_link', 
	'year_link', 
); 

foreach ( $filters as $filter ) {
    add_filter( $filter, 'wp_make_link_relative' );
}
