<?php
/**
 * NalarTekno theme functions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'NALARTEKNO_VERSION', '1.4.0' );

function dgworld_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'automatic-feed-links' );

	add_image_size( 'dgworld-hero', 900, 560, true );
	add_image_size( 'dgworld-card', 500, 320, true );

	register_nav_menus( array(
		'primary' => __( 'Menu Utama', 'dgworld' ),
		'footer'  => __( 'Menu Footer', 'dgworld' ),
	) );
}
add_action( 'after_setup_theme', 'dgworld_setup' );

function dgworld_scripts() {
	wp_enqueue_style(
		'dgworld-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800&family=Source+Serif+4:opsz,wght@8..60,500;8..60,600;8..60,700;8..60,800&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'dgworld-style', get_stylesheet_uri(), array( 'dgworld-fonts' ), NALARTEKNO_VERSION );
	wp_enqueue_script( 'dgworld-script', get_template_directory_uri() . '/script.js', array(), NALARTEKNO_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'dgworld_scripts' );

function dgworld_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'dgworld_resource_hints', 10, 2 );

function dgworld_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'dgworld' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<div class="widget card"><div class="card-body">',
		'after_widget'  => '</div></div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	) );

	$ad_slots = array(
		'ad-home-after-hero'     => array(
			'name'        => __( 'Iklan: Homepage setelah Hero', 'dgworld' ),
			'description' => __( 'Banner horizontal setelah artikel unggulan di homepage.', 'dgworld' ),
		),
		'ad-home-between-sections' => array(
			'name'        => __( 'Iklan: Homepage antar Bagian', 'dgworld' ),
			'description' => __( 'Banner horizontal sebelum bagian tutorial di homepage.', 'dgworld' ),
		),
		'ad-article-inline'      => array(
			'name'        => __( 'Iklan: Tengah Artikel', 'dgworld' ),
			'description' => __( 'Disisipkan setelah paragraf ketiga pada artikel.', 'dgworld' ),
		),
		'ad-article-after'       => array(
			'name'        => __( 'Iklan: Setelah Artikel', 'dgworld' ),
			'description' => __( 'Ditampilkan setelah isi artikel dan sebelum navigasi seri.', 'dgworld' ),
		),
	);

	foreach ( $ad_slots as $id => $slot ) {
		register_sidebar( array(
			'name'          => $slot['name'],
			'id'            => $id,
			'description'   => $slot['description'],
			'before_widget' => '<div class="ad-widget %2$s" id="%1$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="screen-reader-text">',
			'after_title'   => '</h2>',
		) );
	}
}
add_action( 'widgets_init', 'dgworld_widgets_init' );

/**
 * Render an advertising widget area only when it contains a widget.
 */
function dgworld_render_ad_slot( $slot_id, $modifier = '' ) {
	$classes = trim( 'ad-slot ' . sanitize_html_class( $modifier ) );
	?>
	<aside class="<?php echo esc_attr( $classes ); ?>" aria-label="<?php esc_attr_e( 'Iklan', 'dgworld' ); ?>">
		<span class="ad-label"><?php esc_html_e( 'Iklan', 'dgworld' ); ?></span>
		<div class="ad-content">
			<?php if ( is_active_sidebar( $slot_id ) ) : ?>
				<?php dynamic_sidebar( $slot_id ); ?>
			<?php else : ?>
				<span class="ad-placeholder"><?php esc_html_e( 'Ruang iklan', 'dgworld' ); ?></span>
			<?php endif; ?>
		</div>
	</aside>
	<?php
}

/**
 * Insert the inline advertisement after the third paragraph of single posts.
 */
function dgworld_insert_inline_ad( $content ) {
	if ( is_admin() || is_feed() || ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	ob_start();
	dgworld_render_ad_slot( 'ad-article-inline', 'ad-slot-inline' );
	$ad_markup = ob_get_clean();

	$paragraph_count = 0;
	$inserted        = false;
	$output          = preg_replace_callback(
		'/<\/p>/i',
		function ( $matches ) use ( &$paragraph_count, &$inserted, $ad_markup ) {
			$paragraph_count++;
			if ( 3 === $paragraph_count ) {
				$inserted = true;
				return $matches[0] . $ad_markup;
			}
			return $matches[0];
		},
		$content
	);

	return $inserted ? $output : $content . $ad_markup;
}
add_filter( 'the_content', 'dgworld_insert_inline_ad', 20 );

// Shorter, cleaner excerpts for card layout.
function dgworld_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'dgworld_excerpt_length' );

function dgworld_excerpt_more( $more ) {
	return '…';
}
add_filter( 'excerpt_more', 'dgworld_excerpt_more' );

function dgworld_default_menu() {
	echo '<ul><li><a href="' . esc_url( home_url( '/' ) ) . '">Beranda</a></li></ul>';
}

function dgworld_first_category_name() {
	$cats = get_the_category();
	return ! empty( $cats ) ? $cats[0]->name : 'NalarTekno';
}

function dgworld_format_date( $timestamp = null, $include_weekday = false ) {
	$timestamp = $timestamp ?: current_time( 'timestamp' );
	$months = array( 1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember' );
	$days   = array( 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu' );
	$date   = wp_date( 'j', $timestamp ) . ' ' . $months[ (int) wp_date( 'n', $timestamp ) ] . ' ' . wp_date( 'Y', $timestamp );

	return $include_weekday ? $days[ (int) wp_date( 'w', $timestamp ) ] . ', ' . $date : $date;
}

function dgworld_post_date( $post = null ) {
	return dgworld_format_date( get_post_timestamp( $post ) );
}

function dgworld_reading_time( $post = null ) {
	$content = get_post_field( 'post_content', $post ?: get_the_ID() );
	$words   = str_word_count( wp_strip_all_tags( $content ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

function dgworld_pagination() {
	the_posts_pagination( array(
		'mid_size'  => 2,
		'prev_text' => '&larr; Sebelumnya',
		'next_text' => 'Berikutnya &rarr;',
		'class'     => 'pagination',
	) );
}

/**
 * Recognized tutorial-level category slugs, in progression order.
 * Used to build the homepage level columns and the level pill strip.
 */
function dgworld_tutorial_levels() {
	return array(
		'tutorial-basic'        => array(
			'label' => 'Basic', 'class' => 'basic',
			'title' => 'Bangun fondasi digital',
			'description' => 'Kenali internet, browser, email, keamanan dasar, dan cara kerja web.',
			'outcome' => 'Cocok untuk mulai dari nol',
		),
		'tutorial-intermediate' => array(
			'label' => 'Intermediate', 'class' => 'intermediate',
			'title' => 'Mulai membuat produk web',
			'description' => 'Pelajari HTML, CSS, JavaScript, Git, dan cara menerbitkan proyek pertama.',
			'outcome' => 'Cocok setelah memahami dasar',
		),
		'tutorial-advanced'     => array(
			'label' => 'Advanced', 'class' => 'advanced',
			'title' => 'Bangun aplikasi terhubung',
			'description' => 'Dalami API, database, autentikasi, pengujian, dan arsitektur aplikasi.',
			'outcome' => 'Untuk pembuat aplikasi aktif',
		),
		'tutorial-expert'       => array(
			'label' => 'Expert', 'class' => 'expert',
			'title' => 'Rancang sistem berskala',
			'description' => 'Pelajari skalabilitas, observabilitas, keamanan, cloud, dan keputusan arsitektur.',
			'outcome' => 'Untuk pengembangan profesional',
		),
	);
}

/**
 * Renders prev/next links within the same tutorial-level category,
 * ordered by publish date, so a series reads top to bottom.
 */
function dgworld_series_nav() {
	$levels = array_keys( dgworld_tutorial_levels() );
	$cat    = get_the_category();
	if ( empty( $cat ) ) return;

	$slugs = wp_list_pluck( $cat, 'slug' );
	$level_slug = array_intersect( $slugs, $levels );
	if ( empty( $level_slug ) ) return;
	$level_slug = reset( $level_slug );

	$category = get_category_by_slug( $level_slug );
	if ( ! $category ) return;

	global $post;
	$args = array(
		'cat'            => $category->term_id,
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'ASC',
		'fields'         => 'ids',
	);
	$ids = get_posts( $args );
	$pos = array_search( $post->ID, $ids );
	if ( false === $pos ) return;

	$prev_id = isset( $ids[ $pos - 1 ] ) ? $ids[ $pos - 1 ] : 0;
	$next_id = isset( $ids[ $pos + 1 ] ) ? $ids[ $pos + 1 ] : 0;

	if ( ! $prev_id && ! $next_id ) return;
	?>
	<div class="series-nav">
		<div>
			<?php if ( $prev_id ) : ?>
				<a href="<?php echo esc_url( get_permalink( $prev_id ) ); ?>">&larr; <?php echo esc_html( get_the_title( $prev_id ) ); ?></a>
			<?php endif; ?>
		</div>
		<div style="text-align:right">
			<?php if ( $next_id ) : ?>
				<a href="<?php echo esc_url( get_permalink( $next_id ) ); ?>"><?php echo esc_html( get_the_title( $next_id ) ); ?> &rarr;</a>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
