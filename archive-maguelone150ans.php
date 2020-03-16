<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 */

require_once __DIR__ . '/common/date_utils.php';

get_header();

// Remove the "ARCHIVES: " text
add_filter( 'get_the_archive_title', 'wpsite_archive_title_remove_prefix' );
function wpsite_archive_title_remove_prefix( $title ) {
	if ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	}

	return $title;
}


global $post;
global $wp_query;

$args = array_merge( $wp_query->query, array( 'order' => 'ASC', 'nopaging' => true ) ); // Classement des post + no paging
query_posts( $args );

/**
 * @param $post
 *
 * @return string le titre du post
 */
function getTitle( $post ): string {
	$result = get_post_field( 'event_title', $post, 'raw' );
	if ( empty( $result ) ) {
		$result = get_post_field( 'post_title', $post, 'raw' );
	}

	return $result;
}



if ( have_posts() ) { ?>

    <header class="page-header">

		<?php the_archive_title( '<h1 class="archive-title">', '</h1>' ); ?>

    </header><!-- .page-header -->

    <style>
        .colonne {
            font-weight: bold;
            background-color: #eeeeee;
        }

        .ligne:nth-child(even) {
            background: #f9f9f9
        }

        .ligne:nth-child(odd) {
            background: #FFF
        }

        .pellet {
            display: inline-block;
            width:  22px;
            height: 22px;
            border-radius: 50%;
            -ms-transform: rotate(45deg);     /* IE 9 */
            -webkit-transform: rotate(45deg); /* Chrome, Safari, Opera */
            transform: rotate(45deg);
            vertical-align: middle;
        }

        .checkmark:before {
            content:"";
            position: absolute;
            width:3px;
            height:9px;
            background-color:#fff;
            left:11px;
            top:6px;
        }

        .checkmark:after {
            content:"";
            position: absolute;
            width:3px;
            height:3px;
            background-color:#fff;
            left:8px;
            top:12px;
        }
    </style>

    <div id="post-wrapper" class="post-wrapper clearfix">

        <div id="headimg" class="header-image featured-image-header">
            <img src="https://espritdeliberte.leswoody.net/wp-content/uploads/2019/11/logo150ans-1182x480.jpg" class="attachment-poseidon-header-image size-poseidon-header-image wp-post-image" alt="" width="1182" height="480">
        </div>

        <br>
        <center> <i>Le temple de la rue de Maguelone est un lieu au cœur de Montpellier.</i> </center>
        <p style="color: chocolate">
        Chers amis, chers Frères et Soeurs</><br>
            <br>
            Suite aux annonces du Président de la République et de son Premier Ministre,<br>
            nous sommes contraints d'annuler dans l'immédiat quelques rencontres inscrites à l'agenda de cette fête.<br>
            Nous ferons notre possible pour simplement les reporter, et nous vous tiendrons informés, le cas échéant, des nouvelles dates.<br>
            D'ici là, portez-vous bien et prenez soin de vous.<br>
            <br>
            L'équipe des 150 ans !
        </p>
        <p>
        Depuis 1870, il permet à la communauté protestante de se rassembler pour y célébrer un culte ouvert sur la vie.
        C’est un lieu pour la musique, pour des expositions, des conférences, pour penser la vie et la célébrer en toute fraternité.
        C’est un lieu qui a connu la militance pour la laïcité, la résistance au totalitarisme, l’action pour l’égalité homme-femme.
        C’est un lieu qui œuvre pour le respect de la création, pour l’accueil inconditionnel des personnes.
        C’est un lieu pour transcender les clivages et apprendre l’art de vivre ensemble.
        C’est un lieu pour porter la vie à son incandescence.
        </p>
        <p align="center">
            C’est cela que nous voulons fêter avec vous tout au long de cette année 2020.<br>
<!--            <b>Le programme au format PDF est <a download href="https://espritdeliberte.leswoody.net/wp-content/uploads/2019/12/LIVRET-150-ANS-FINALISE-low.pdf">téléchargeable ici</a></b> -->
            <s>Le programme au format PDF est téléchargeable ici</s>
        </p>
        <table>
            <thead>
                <tr align="center">
                    <td class="colonne" width="50px">Type</td>
                    <td class="colonne" width="230px">Date</td>
                    <td class="colonne" width="70px">Heure</td>
                    <td class="colonne" >Evénement</td>
                    <td class="colonne" width="250px">Conférencier</td>
                </tr>
            </thead>
            <tbody>
                <?php

                $dtUtil = new DateUtils();

                // Recherche de la categorie m150ans et des sous-catégories.
                // La recherche du post de replay exclue systématiquement ces catégories, il faut les rajouter si on veut les voir apparaitre.
                $cat_m150     = get_category_by_slug( 'maguelone150ans' );
                $all_cat_m150 = category_has_children( $cat_m150->term_id );

                //$nom_jour_fr = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
                $mois_fr = [ 1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril', 5=> 'mai', 6 => 'juin',
                             7 => 'juillet', 8 => 'août', 9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre' ];

                //$tomorrowTS = mktime(0, 0, 0, date( 'm' )  , date( 'd' ) + 1, date( 'Y' ));
                $todayTS = mktime(0, 0, 0, date( 'm' ),date( 'd' ),date( 'Y' ));

                while ( have_posts() ) { the_post();
					$speakerValue = get_field('speaker_name' );
                    $event_type   = get_field('event_type');
					$dtEvent      = get_field('start_date_time_event');

                    $dtColorEvent = '#' . getEventColor_m150ans( $event_type );

                    // Analyse de la date pour détecter les événements passés
	                $tab = explode(' à ', $dtEvent );
	                [ $dateEvent, $timeEvent ] = $tab;
	                $tab = explode(' ', $dateEvent );
	                [ $day_name, $day, $month, $year ] = $tab;

                    $month = array_search( $month, $mois_fr, true );
	                $dateEventTS = mktime(0, 0, 0, $month, $day, $year);
	                $dateEventPlus1DayTS = mktime(0, 0, 0, $month, $day + 1, $year);
                    $dateEventPlus1WeekTS = mktime(0, 0, 0, $month, $day + 7, $year);
	                $pelletClassname = 'pellet';
	                $textColor = $dtColorEvent;

                    $cancelledDate = $dtUtil->getCancelledDateComment($dateEventTS);
                    $isCancelled   = ( $cancelledDate !== '' );
	                if ( $isCancelled ) {
		                $textColor = '#000000';
		                $dtColorEvent = '#000000';
	                }

	                $replayPost = $post;
	                if ( ! $isCancelled && $dateEventTS < $todayTS ) { // Si la date est passée
		                $pelletClassname .= ' checkmark';
		                $textColor        = '#696969'; // gris

		                // Recherche du post de replay
                        $found = false;
		                if ( $speakerValue !== 'James Woody' ) {
                            $posts = get_posts_between( $dateEventTS, $dateEventPlus1WeekTS, $all_cat_m150 );
                            foreach ( $posts as $replayedPost ) {
			                    $replayedTitle = getTitle( $replayedPost );
				                if ( startsWith( $replayedTitle, $speakerValue ) ) { // On cherche un article qui commence par le nom du conférencier
					                $replayPost = $replayedPost;
					                $found = true;
					                break;
				                }
			                }
		                }

		                if ( ! $found ) {
			                $posts = get_posts_between( $dateEventTS, $dateEventPlus1DayTS ); // On serre l'etau pour trouver le post
			                if ( is_array($posts ) ) {
			                    $replayPost = end($posts );
                            }
                        }
	                }

	                if ( $isCancelled ) {
		                $dateEvent    = '<del>' . $dateEvent . '</del>';
		                $timeEvent    = '<del>' . $timeEvent . '</del>';
		                $title_event  = '<del>' . getTitle( $replayPost ) . '</del>' . ' : ' . $cancelledDate;
		                $speakerValue = '<del>' . $speakerValue . '</del>';
	                } else {
		                $title_event = "<a href='" . get_the_permalink( $replayPost ) .
		                               "' style='color: " . $textColor . "' target='_blank'>" . getTitle( $replayPost ) . '</a>';
                    }

                    ?>

                    <tr class="ligne">
                        <td align="center"><span class="<?php echo $pelletClassname ?>" style="background: <?php echo $dtColorEvent ?>" ></span></td>
                        <td><?php echo "<span style='color: " . $textColor . ";'>" . $dateEvent . '</span>'; ?></td>
                        <td align="center"><?php echo "<span style='color: " . $textColor . ";'>" . $timeEvent . '</span>'; ?></td>
                        <td><?php echo "<span style='color: " . $textColor . ";'>" . $title_event  . '</span>'; ?></td>
                        <td><?php echo "<span style='color: " . $textColor . ";'>" . $speakerValue . '</span>'; ?></td>
                    </tr>

                    <?php
                } ?>

            </tbody>
        </table>
    </div>

	<?php

	wp_reset_postdata();
} else {
    get_template_part( 'template-parts/content', 'none' );
}

get_footer();
