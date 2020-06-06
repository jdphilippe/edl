<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Poseidon
 */

get_header();

$THEME_PATH  = get_stylesheet_directory_uri();
$COMMON_PATH = $THEME_PATH . "/common";

$param = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_URL);

// Get Theme Options from Database.
$theme_options = poseidon_theme_options();
?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) : ?>

			<header class="page-header">

				<?php the_archive_title( '<h1 class="archive-title">', '</h1>' ); ?>
				<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>

			</header><!-- .page-header -->

			<div id="post-wrapper" class="post-wrapper clearfix">

				<?php while ( have_posts() ) : the_post();

					get_template_part( 'template-parts/content', esc_attr( $theme_options['post_content'] ) );

				endwhile; ?>

			</div>

			<?php poseidon_pagination();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->

	<?php get_sidebar(); ?>

<?php get_footer();
