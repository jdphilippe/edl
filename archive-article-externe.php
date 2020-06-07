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
$COMMON_PATH = $THEME_PATH . '/common';

global $wp_query;

$args = array_merge( $wp_query->query, array( 'nopaging' => true ) ); // Classement des post + no paging
query_posts( $args );


function isMobile() {
	//return 1;
	return wp_is_mobile();
}

$inc_common_ui = __DIR__ . '/common/common' . ( isMobile() ? '_mobile' : '' ) . '.php'; // pour inclure common.php ou common_mobile.php selon le cas
require_once $inc_common_ui;

$param = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_URL);

// Get Theme Options from Database.
$theme_options = poseidon_theme_options();
?>
    <script>
        function ExternalPost(displayDate, timestamp, title, media, typeOfMedia) {
            this.date = {
                display: displayDate,
                timestamp: timestamp
            };

            this.title = title;
            this.media = media;
            this.typeOfMedia = typeOfMedia;
        }

        function getData() {
            let result = [];

            <?php
                $width  = 78;
                $height = 52;
                $paddingTop = 14;
                $paddingLeft = 10;
	            $dateUtils = new DateUtils();

	            function getImage( $text) {
	                switch ($text) {
                        case 'texte':
                            return 'document.png';
                            break;

                        case 'audio':
                            return 'button_play.png';
                            break;

                        case 'video':
                            return 'tv.png';
                            break;

                        default:
                            return $text;
                    }
                }

                while ( have_posts() ) {
		            the_post();

		            $title   = get_the_title();
		            $tooltip = $title;

		            if ( ! isMobile() ) {
			            $image = get_the_post_thumbnail_url( $post, [ $width, $height ] );

			            // On met l'image par defaut si non definie
			            if ( $image === null || $image === false ) {
				            $image = 'https://espritdeliberte.leswoody.net/wp-content/uploads/2017/08/v6.png';
			            }

			            // On place sur la gauche du titre l'image de l'article
			            $tooltip = $title;
			            $title =
				            "<div style='overflow: hidden; max-height: " . $height . "px; float: left' >" .
				            "<img src='" . $image . "' width='" . $width . "px' alt='" . $tooltip . "' />" .
				            '</div>' .
				            "<div style='word-wrap: break-word; padding-top: " . $paddingTop . 'px; padding-left: ' . $paddingLeft . "px; display: inline-flex' >" .
				            $title .
				            '</div>';
		            }

		            $title = addslashes( "<a href='" . get_post_permalink($post->ID) . "' target='_blank' title='" . $tooltip . "'>" . $title . '</a>' );

	                $date           = get_field('date');
                    $mediaName      = get_field('nom_du_media');
                    $mediaTypeCB    = get_field('type_de_media');
                    $link           = get_field('lien');

                    // Parse post link to set on media name
                    $mediaLink = '';
                    $postLink = '';
                    if (! empty( $link )) {
	                    $urlTab = parse_url($link );
	                    $mediaLink = $urlTab[ 'scheme' ] . '://' . $urlTab[ 'host' ];
	                    $mediaName = addslashes( "<a href='" . $mediaLink . "' target='_blank' title='" . $mediaName . "'>" . $mediaName . '</a>' );
	                    $postLink = $link;
                    }

		            $mediaType = '';
                    if ( ! empty( $mediaTypeCB )) {
                        $text = $mediaTypeCB[0];
                        $mediaIcon = getImage($text);
                        $mediaType = "<img src='" . $COMMON_PATH . '/images/' . $mediaIcon . "' style='vertical-align:middle;' alt='" . $text . "' title='Ouvrir l&apos;article' />";
                        if (! empty( $postLink )) {
	                        $mediaType = addslashes( "<a href='" . $postLink . "' target='_blank'><div align='center'>" . $mediaType . '</div></a>' );
                        }
                    }

                    $tmp = explode('/', $date);
	                $usDate = $tmp[1] . '/' . $tmp[0] . '/' . $tmp[2];
                    $date = new DateTime($usDate);
	                $formatDate  = isMobile() ? 'd/m' : 'd/m/Y';
                    $displayDate = date_format($date, $formatDate);
	                $timestamp   = date_format($date, 'U');

	                if ( ! isMobile() ) {
		                $comment = $dateUtils->getComment( $timestamp );
		                if ( $comment !== '' ) {
			                $displayDate .= '<br><span style="color: chocolate;">' . $comment . '</span>';
		                }
	                }

	                echo "result.push( new ExternalPost('" . $displayDate . "', '" . $timestamp . "', '" . $title . "', '" . $mediaName . "', '" . trim($mediaType ) . "' ));\n";
 	            }
            ?>

            return result;
        }

        (function (window, $, undefined) {

            function isMobile() {
                //return "1";
                return ("<?= isMobile() ?>" === "1");
            }

            function isScrolledIntoView(elem)
            {
                const docViewTop = $(window).scrollTop();
                const docViewBottom = docViewTop + $(window).height();

                const elemTop = $(elem).offset().top;
                const elemBottom = elemTop + $(elem).height();

                return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
            }

            $(document).ready(function () {

                let category = "publication-externe",
                    fname = category,
                    oldHeaderFixedValue = 0,
                    sortable = ! isMobile(),
                    dateColWidth = isMobile() ? 80 : 100;

                if (isMobile())
                    fname += "_mobile";

                fname += ".json";

                let table = $('#publications-externe').DataTable({
                    bJQueryUI: ! isMobile(),
                    dom: 'frtip', // Enleve la barre d'outils et de statut
                    fixedHeader: true,
                    paging: false,
                    sort: sortable,
                    responsive: true,
                    language: {
                        url: "<?php echo $COMMON_PATH ?>/json/traduction_external-post.json"
                    },
                    data: getData(),
                    columns: [
                        {
                            data: {
                                _:    "date.display",
                                sort: "date.timestamp"
                            }
                        },
                        {data: "title"   },
                        {data: "media"   },
                        {data: "typeOfMedia", bSortable: false, bSearchable: false }
                    ],
                    columnDefs: [
                        {width: dateColWidth, targets: 0}, // Date
                        {width: 120, targets: 2}, // Media
                        {width: 60,  targets: 3}  // Type (lien)
                    ],
                    order: [[0, "desc"]],
                    fnInitComplete: function () {

                        // Enleve les accents du champ de recherche
                        let fnSearch = function () {
                                table
                                    .search(
                                        jQuery.fn.DataTable.ext.type.search.string(this.value)
                                    )
                                    .draw(false); // Pour eviter un clignotement du tableau
                            },
                            $searchField = $("input[type='search']");

                        if ( isMobile() ) {
                            $searchField
                                .off()
                                .on("keyup input", fnSearch);
                        } else {
                            // Remove accented character from search input as well
                            $searchField
                                .off()
                                .keyup(fnSearch)
                                .addClass("mousetrap") // Pour qu'un Ctrl +f dessus n'active pas la recherche par defaut
                                .focus();

                            // Map le CTRL +F sur le champ Recherche
                            Mousetrap.bind("mod+f", function () {
                                $searchField.focus();
                                return false;
                            });
                        }
                    }
                });

                if (isMobile()) {
                } else {
                    $(window).scroll(function() {
                        var headerOffset = isScrolledIntoView(".header-main" ) ? $( ".header-main" ).height() : 0;

                        if (headerOffset === oldHeaderFixedValue)
                            return;

                        headerOffset += $("#wpadminbar").height();
                        table.fixedHeader.headerOffset(headerOffset);
                        table.fixedHeader.adjust();
                        oldHeaderFixedValue = headerOffset;
                    });
                }
            });
        })(window, jQuery);
    </script>

    <table class="dataTable display responsive table table-striped table-hover" id="publications-externe" style="width: 100%;">
        <thead>
        <tr>
            <th style='text-align: center' class="all">Date</th>
            <th style='text-align: center' class="all">Titre</th>
            <th style='text-align: center' class="min-tablet-l">Média</th>
            <th style='text-align: center' class="min-tablet-p">Type</th>
        </tr>
        </thead>
    </table>

<?php

wp_reset_postdata();

get_footer();
