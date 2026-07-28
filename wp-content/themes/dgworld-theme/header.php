<?php if ( ! defined( 'ABSPATH' ) ) exit; ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#0d3157">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content">Lewati ke konten utama</a>

<header class="site-header">
	<div class="news-utility">
		<div class="container news-utility-inner">
			<span><?php echo esc_html( dgworld_format_date( null, true ) ); ?></span>
			<span>Belajar teknologi dari dasar sampai mahir</span>
		</div>
	</div>
	<div class="container site-header-inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="brand-mark">DG</span><span>world</span></a></p>
			<?php endif; ?>
		</div>
		<nav class="primary-nav" id="primary-navigation" aria-label="Menu Utama">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'fallback_cb'    => 'dgworld_default_menu',
			) );
			?>
			<form class="mobile-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label for="mobile-search">Cari tutorial atau berita</label>
				<div><input id="mobile-search" type="search" name="s" placeholder="Contoh: HTML dasar…" value="<?php echo esc_attr( get_search_query() ); ?>"><button type="submit">Cari</button></div>
			</form>
		</nav>
		<div class="header-actions">
			<form class="header-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="header-search">Cari tutorial atau berita</label>
				<input id="header-search" type="search" name="s" placeholder="Cari topik…" value="<?php echo esc_attr( get_search_query() ); ?>">
				<button type="submit">Cari</button>
			</form>
			<button class="nav-toggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="primary-navigation">
				<span aria-hidden="true"></span><span aria-hidden="true"></span>
			</button>
		</div>
	</div>
	<div class="container level-strip">
		<span class="level-strip-label">Jalur belajar:</span>
		<?php foreach ( dgworld_tutorial_levels() as $slug => $data ) :
			$term = get_category_by_slug( $slug );
			if ( ! $term ) continue;
			?>
			<a class="level-pill <?php echo esc_attr( $data['class'] ); ?>" href="<?php echo esc_url( get_category_link( $term ) ); ?>">
				Tutorial <?php echo esc_html( $data['label'] ); ?>
			</a>
		<?php endforeach; ?>
	</div>
</header>
