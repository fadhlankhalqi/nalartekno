<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
	<footer class="site-footer">
		<div class="container">
			<div class="footer-grid">
				<div>
					<h4>NalarTekno</h4>
					<p><?php bloginfo( 'description' ); ?></p>
				</div>
				<div>
					<h4>Kategori</h4>
					<ul>
						<?php foreach ( dgworld_tutorial_levels() as $slug => $data ) :
							$term = get_category_by_slug( $slug );
							if ( ! $term ) continue; ?>
							<li><a href="<?php echo esc_url( get_category_link( $term ) ); ?>">Tutorial <?php echo esc_html( $data['label'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div>
					<h4>Menu</h4>
					<ul>
						<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'fallback_cb' => false, 'items_wrap' => '%3$s' ) ); ?>
						<?php if ( ! has_nav_menu( 'footer' ) ) : ?>
							<li><a href="<?php echo esc_url( home_url( '/tentang/' ) ); ?>">Tentang NalarTekno</a></li>
							<li><a href="<?php echo esc_url( home_url( '/pedoman-editorial/' ) ); ?>">Pedoman Editorial</a></li>
							<li><a href="<?php echo esc_url( home_url( '/kontak/' ) ); ?>">Kontak</a></li>
							<li><a href="<?php echo esc_url( get_privacy_policy_url() ?: home_url( '/kebijakan-privasi/' ) ); ?>">Kebijakan Privasi</a></li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
			<div class="footer-bottom">
				&copy; <?php echo esc_html( date( 'Y' ) ); ?> NalarTekno. Semua hak dilindungi.
			</div>
		</div>
	</footer>
<?php wp_footer(); ?>
</body>
</html>
