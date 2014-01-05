<?php
/**
 * @package Chunk
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 580;

/**
 * Setup Chunk
 */
function chunk_setup() {
	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 */
	load_theme_textdomain( 'chunk', get_template_directory_uri() . '/languages' );

	/**
	 * Load up our functions for grabbing content from posts
	 */
	require( get_template_directory() . '/content-grabbers.php' );

	/**
	 * Add feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link', 'gallery', 'chat', 'audio' ) );

	/**
	 * Add custom background support.
	 */
	add_theme_support( 'custom-background' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Main Menu', 'chunk' ),
	) );
}
add_action( 'after_setup_theme', 'chunk_setup' );


/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function chunk_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'chunk_page_menu_args' );

/**
 * Register our footer widget area
 */
function chunk_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Footer', 'chunk' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'chunk_widgets_init' );

/**
 * Enqueue scripts and styles
 *
 * @since Chunk 1.1
 */
function chunk_general_scripts() {
	wp_enqueue_style( 'chunk-style', get_stylesheet_uri() );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'chunk_general_scripts' );

/**
 * Enqueue font styles.
 */
function chunk_fonts() {
	$protocol = is_ssl() ? 'https' : 'http';
	wp_enqueue_style( 'oswald', "$protocol://fonts.googleapis.com/css?family=Oswald&subset=latin,latin-ext" );
}
add_action( 'wp_enqueue_scripts', 'chunk_fonts' );

/**
 * Audio player.
 */
function chunk_scripts() {
	if ( ! is_singular() || ( is_singular() && 'audio' == get_post_format() ) )
		wp_enqueue_script( 'audio-player', get_template_directory_uri() . '/js/audio-player.js', array( 'swfobject' ), '20120525' );
}
add_action( 'wp_enqueue_scripts', 'chunk_scripts' );

function chunk_add_audio_support() {
	if ( ! is_singular() || ( is_singular() && 'audio' == get_post_format() ) ) {
?>
		<script type="text/javascript">
			AudioPlayer.setup( "<?php echo get_template_directory_uri(); ?>/swf/player.swf", {
				bg: "e4e4e4",
				leftbg: "e4e4e4",
				rightbg: "e4e4e4",
				track: "222222",
				text: "555555",
				lefticon: "eeeeee",
				righticon: "eeeeee",
				border: "e4e4e4",
				tracker: "eb374b",
				loader: "666666"
			});
		</script>
<?php }
}
add_action( 'wp_head', 'chunk_add_audio_support' );

/**
 * Custom Classes
 */
function chunk_body_classes( $classes ) {
	if ( is_multi_author() ) {
		$classes[] = 'multiple-authors';
	} else {
		$classes[] = 'single-author';
	}

	return $classes;
}
add_filter( 'body_class', 'chunk_body_classes' );

/**
 * Date formats for Chunk.
 */
function chunk_date() {
	$date_format = get_option( 'date_format' );
	if ( 'F j, Y' == $date_format ) :
		the_time( 'M d Y' );
	else:
		the_time( $date_format );
	endif;
}

/**
 * Appends post title to Aside and Quote posts
 *
 * @param string $content
 * @return string
 */
function chunk_conditional_title( $content ) {

	if ( has_post_format( 'aside' ) || has_post_format( 'quote' ) ) {
		if ( ! is_singular() )
			$content .= the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( sprintf( __( 'Permalink to %s', 'chunk' ), the_title_attribute( 'echo=0' ) ) ) . '" rel="bookmark">', '</a></h2>', false );
		else
			$content .= the_title( '<h2 class="entry-title">', '</h1>', false );
	}

	return $content;
}
add_filter( 'the_content', 'chunk_conditional_title', 0 );

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @since Chunk 1.1
 */
function chunk_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'chunk' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'chunk_wp_title', 10, 2 );

/**
 * Implement the Custom Header feature
 */
require( get_template_directory() . '/inc/custom-header.php' );