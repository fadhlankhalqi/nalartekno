<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<article class="card">
	<a href="<?php the_permalink(); ?>" class="thumb">
		<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'dgworld-card' ); else : ?>
			<span class="thumb-placeholder <?php echo esc_attr( sanitize_html_class( get_post_type() . '-' . dgworld_first_category_name() ) ); ?>" aria-hidden="true">
				<span>NT</span><strong><?php echo esc_html( dgworld_first_category_name() ); ?></strong>
			</span>
		<?php endif; ?>
	</a>
	<div class="card-body">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 16 ) ); ?></p>
		<div class="meta"><?php echo esc_html( dgworld_post_date() ); ?> · <?php echo esc_html( dgworld_reading_time() ); ?> menit baca</div>
	</div>
</article>
