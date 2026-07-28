<?php if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
while ( have_posts() ) : the_post();
	?>
	<main class="container single-wrap" id="main-content">
		<header class="single-header">
			<h1><?php the_title(); ?></h1>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="single-thumb"><?php the_post_thumbnail( 'dgworld-hero' ); ?></div>
		<?php endif; ?>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</main>
	<?php
endwhile;
get_footer();
