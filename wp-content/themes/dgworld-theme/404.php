<?php if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<main class="container" id="main-content">
	<div class="not-found">
		<h1>404</h1>
		<p>Halaman yang Anda cari tidak ditemukan atau sudah dipindahkan.</p>
		<?php get_search_form(); ?>
		<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Kembali ke Beranda</a></p>
	</div>
</main>
<?php get_footer(); ?>
