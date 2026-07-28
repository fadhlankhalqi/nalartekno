<?php if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$berita_term = get_category_by_slug( 'berita-digital' );
$levels      = dgworld_tutorial_levels();
?>

<main class="container" id="main-content">
	<section class="learning-hero" aria-labelledby="learning-title">
		<div class="learning-hero-copy">
			<p class="learning-kicker">Belajar terarah, praktik bertahap</p>
			<h1 id="learning-title">Mulai dari dasar.<br>Naik level dengan percaya diri.</h1>
			<p>Ikuti jalur belajar teknologi yang tersusun dari Basic sampai Expert. Setiap materi membantu Anda membangun kemampuan yang bisa langsung dipraktikkan.</p>
			<div class="learning-actions">
				<?php $basic_term = get_category_by_slug( 'tutorial-basic' ); ?>
				<?php if ( $basic_term ) : ?>
					<a class="button-primary" href="<?php echo esc_url( get_category_link( $basic_term ) ); ?>">Mulai dari Basic</a>
				<?php endif; ?>
				<a class="button-secondary" href="#jalur-belajar">Lihat semua level</a>
			</div>
		</div>
		<div class="learning-map" aria-label="Urutan jalur belajar">
			<?php foreach ( $levels as $index => $data ) : ?>
				<div class="learning-step <?php echo esc_attr( $data['class'] ); ?>">
					<span><?php echo esc_html( sprintf( '%02d', array_search( $index, array_keys( $levels ), true ) + 1 ) ); ?></span>
					<strong><?php echo esc_html( $data['label'] ); ?></strong>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
	$ticker_posts = get_posts( array(
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => true,
	) );
	if ( ! empty( $ticker_posts ) ) :
		$ticker_post = $ticker_posts[0];
		?>
		<div class="news-ticker" aria-label="Berita terbaru">
			<strong>Terbaru</strong>
			<a href="<?php echo esc_url( get_permalink( $ticker_post ) ); ?>"><?php echo esc_html( get_the_title( $ticker_post ) ); ?></a>
			<time datetime="<?php echo esc_attr( get_the_date( 'c', $ticker_post ) ); ?>"><?php echo esc_html( dgworld_post_date( $ticker_post ) ); ?></time>
		</div>
	<?php endif; ?>

	<?php
	// Hero: latest post overall as the big feature, next 2 as side cards.
	$hero_query = new WP_Query( array( 'posts_per_page' => 3, 'ignore_sticky_posts' => true ) );
	if ( $hero_query->have_posts() ) :
		$i = 0;
		?>
		<div class="hero">
			<?php while ( $hero_query->have_posts() ) : $hero_query->the_post(); $i++;
				if ( 1 === $i ) : ?>
					<article class="hero-main">
						<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'dgworld-hero' ); ?>
						<div class="hero-content">
							<span class="badge"><?php echo esc_html( dgworld_first_category_name() ); ?></span>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<div class="meta"><?php echo esc_html( dgworld_post_date() ); ?> · <?php echo esc_html( dgworld_reading_time() ); ?> menit baca</div>
						</div>
					</article>
					<div class="hero-side">
				<?php else : ?>
					<?php get_template_part( 'template-parts/card' ); ?>
				<?php endif;
			endwhile; ?>
			</div>
		</div>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	<?php dgworld_render_ad_slot( 'ad-home-after-hero', 'ad-slot-wide' ); ?>

	<?php if ( $berita_term ) :
		$berita_query = new WP_Query( array( 'cat' => $berita_term->term_id, 'posts_per_page' => 6 ) );
		if ( $berita_query->have_posts() ) : ?>
			<div class="section-head">
				<h2><span class="bar"></span>Berita Digital</h2>
				<a class="more" href="<?php echo esc_url( get_category_link( $berita_term ) ); ?>">Lihat semua &rarr;</a>
			</div>
			<div class="grid grid-3">
				<?php while ( $berita_query->have_posts() ) : $berita_query->the_post();
					get_template_part( 'template-parts/card' );
				endwhile; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php endif;
	endif; ?>

	<?php dgworld_render_ad_slot( 'ad-home-between-sections', 'ad-slot-wide' ); ?>

	<div class="section-head" id="jalur-belajar">
		<h2><span class="bar"></span>Tutorial: Basic sampai Expert</h2>
	</div>
	<p class="section-intro">Pilih level sesuai kemampuan Anda. Jika baru mulai, ikuti urutannya dari Basic.</p>
	<div class="tutorial-levels">
		<?php foreach ( $levels as $slug => $data ) :
			$term = get_category_by_slug( $slug );
			?>
			<div class="level-col <?php echo esc_attr( $data['class'] ); ?>">
				<div class="level-heading">
					<span class="level-index"><?php echo esc_html( sprintf( '%02d', array_search( $slug, array_keys( $levels ), true ) + 1 ) ); ?></span>
					<div>
						<p><span class="level-dot"></span><?php echo esc_html( $data['label'] ); ?></p>
						<h3><?php echo esc_html( $data['title'] ); ?></h3>
					</div>
				</div>
				<p class="level-description"><?php echo esc_html( $data['description'] ); ?></p>
				<?php if ( $term ) :
					$level_query = new WP_Query( array( 'cat' => $term->term_id, 'posts_per_page' => 5 ) );
					if ( $level_query->have_posts() ) : ?>
						<div class="level-count"><?php echo esc_html( $level_query->found_posts ); ?> materi · <?php echo esc_html( $data['outcome'] ); ?></div>
						<ul>
							<?php while ( $level_query->have_posts() ) : $level_query->the_post(); ?>
								<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
						</ul>
						<a class="level-cta" href="<?php echo esc_url( get_category_link( $term ) ); ?>">Pelajari level <?php echo esc_html( $data['label'] ); ?> <span aria-hidden="true">→</span></a>
						<?php wp_reset_postdata(); ?>
					<?php else : ?>
						<p class="level-empty">Materi level ini sedang disiapkan. Mulai dari level sebelumnya terlebih dahulu.</p>
					<?php endif;
				else : ?>
					<p class="level-empty">Materi level ini sedang disiapkan.</p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>

</main>

<?php get_footer(); ?>
