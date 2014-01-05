<?php 

function feedlove($content) {
$content = $content . '<p class="extra"><a href="http://jarederickson.com/freebies/" title="Jared Erickson" >A minimal wordpress theme by Jared Erickson</a></p>';
return $content;
}
add_filter('the_excerpt_rss', 'feedlove');
add_filter('the_content_rss', 'feedlove');

/* ADD WIDGETS IN THE FOOTER */

		if ( function_exists('register_sidebar') )
		register_sidebar(array(
			'name' => 'bucket left',
			'before_widget' => '<div class="footer-widget">',
			'after_widget' => '</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
	));
		if ( function_exists('register_sidebar') )
		register_sidebar(array(
			'name' => 'bucket right',
			'before_widget' => '<div class="footer-widget">',
			'after_widget' => '</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
	));
	
// Presstrends for tracking how awesome the users are!
	function presstrends() {
	
	// Add your PressTrends and Theme API Keys
	$api_key = '9p6lfk06re8piebz6prtwexjn4qo1cr0g98e';
	$auth = '47ea4rmutxdp8pufv66yu9cjdhf08l0s5';
	
	// NO NEED TO EDIT BELOW
	$data = get_transient( 'presstrends_data' );
	if (!$data || $data == ''){
	$api_base = 'http://api.presstrends.io/index.php/api/sites/add/auth/';
	$url = $api_base . $auth . '/api/' . $api_key . '/';
	$data = array();
	$count_posts = wp_count_posts();
	$comments_count = wp_count_comments();
	$theme_data = get_theme_data(get_stylesheet_directory() . '/style.css');
	$plugin_count = count(get_option('active_plugins'));
	$data['url'] = stripslashes(str_replace(array('http://', '/', ':' ), '', site_url()));
	$data['posts'] = $count_posts->publish;
	$data['comments'] = $comments_count->total_comments;
	$data['theme_version'] = $theme_data['Version'];
	$data['theme_name'] = str_replace( ' ', '', get_bloginfo( 'name' ));
	$data['plugins'] = $plugin_count;
	$data['wpversion'] = get_bloginfo('version');
	foreach ( $data as $k => $v ) {
	$url .= $k . '/' . $v . '/';
	}
	$response = wp_remote_get( $url );
	set_transient('presstrends_data', $data, 60*60*24);
	}}
	
	add_action('wp_head', 'presstrends');

?>