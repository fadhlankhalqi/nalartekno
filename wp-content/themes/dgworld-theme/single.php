<?php if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
while ( have_posts() ) : the_post();
	?>
	<main class="container single-wrap" id="main-content">
		<nav class="breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Beranda</a> / <?php echo esc_html( dgworld_first_category_name() ); ?>
		</nav>
		<header class="single-header">
			<span class="badge"><?php echo esc_html( dgworld_first_category_name() ); ?></span>
			<h1><?php the_title(); ?></h1>
			<div class="single-meta">
				Oleh <?php the_author(); ?> &middot; <?php echo esc_html( get_the_date() ); ?>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="single-thumb"><?php the_post_thumbnail( 'dgworld-hero' ); ?></div>
		<?php endif; ?>

		<div class="entry-content">
			<?php the_content(); ?>
		</div>

		<?php dgworld_render_ad_slot( 'ad-article-after', 'ad-slot-article-end' ); ?>

		<?php dgworld_series_nav(); ?>

		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</main>
	<?php
endwhile;
get_footer();
