<?php
/**
 * The template for displaying article-externe single posts.
 *
 * @package Poseidon
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post();
			get_template_part( 'template-parts/content', 'single' );

			the_field('texte' );

			$date_parution = get_field("date");
			if (! empty($date_parution) ) {
				echo "<i>Date de parution: </i>" . $date_parution;
				echo "<br>";
				echo "<br>";
			}

			$link = get_field("lien");
			if (! empty($link) ) {
			    echo "<i>Lien vers l'intégralité de la publication:</i><br>";
			    echo "<a target='_blank' href='" . $link . "'>" . $link . "</a>";
            }
?>
            <br><br>

            <?php
			poseidon_related_posts();

			comments_template();

		endwhile; ?>

		</main><!-- #main -->
	</section><!-- #primary -->

	<?php get_sidebar(); ?>

<?php get_footer();
