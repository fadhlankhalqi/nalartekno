<?php if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
$term = get_queried_object();
?>
<main class="container" id="main-content">
	<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Beranda</a> / <?php single_cat_title(); ?></nav>
	<div class="section-head">
		<h1><span class="bar"></span><?php single_cat_title(); ?></h1>
	</div>
	<?php if ( ! empty( $term->description ) ) : ?>
		<p class="archive-description"><?php echo esc_html( $term->description ); ?></p>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<div class="grid grid-3">
			<?php while ( have_posts() ) : the_post();
				get_template_part( 'template-parts/card' );
			endwhile; ?>
		</div>
		<?php dgworld_pagination(); ?>
	<?php else : ?>
		<p>Belum ada postingan di kategori ini.</p>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
