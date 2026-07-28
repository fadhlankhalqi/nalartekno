<?php if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<main class="container" id="main-content">
	<div class="section-head">
		<h1><span class="bar"></span><?php the_archive_title(); ?></h1>
	</div>
	<?php if ( have_posts() ) : ?>
		<div class="grid grid-3">
			<?php while ( have_posts() ) : the_post();
				get_template_part( 'template-parts/card' );
			endwhile; ?>
		</div>
		<?php dgworld_pagination(); ?>
	<?php else : ?>
		<p>Belum ada postingan.</p>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
