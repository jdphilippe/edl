<?php
/**
 * The template for displaying maguelone150ans single posts.
 *
 * @package Poseidon
 */


function getImgSrc( $field_id ) {
	$picture = get_field( $field_id );
    return "<img src='" . $picture['sizes']['medium'] ."' alt='" . $picture['title'] . "'>";
}

function getPastille( $class_name ) {
    return "<canvas id='" . $class_name . "' class='" . $class_name . "'></canvas>";
}

function getDateTimeColor( $event_type ) {

	$result = "";
    switch ( $event_type ) {
        case "concert":
	        $result = "41ae4a";
	        break;

        case "conference" :
	        $result = "0056ee";
	        break;

        case "theologie" :
	        $result =  "d2242b";
	        break;
    }

    return $result;
}

function getHtmlList( $field_id, $field_id_after = "", $isItalic = false ) {
    $result = "<ul>";

    $content = strip_tags(get_field($field_id ) );
    $contentTab = explode("\n", $content);
    $afterContentTab = array();
    if ( $field_id_after != "" ) {
        $afterContent = strip_tags(get_field($field_id_after ) );
        $afterContentTab = explode("\n", $afterContent);
    }

    $i = 0;
    foreach ( $contentTab as $line ) {
        if ( empty( $line ) )
            continue;

        $result .= "<li class='li-" . $field_id . "'>" . $line . "</li>";

        // Le texte en after
        if (! empty( $afterContentTab[ $i ] ) ) {
            $styleAttr = "text-align: right; width: 100%;";
            if ( $isItalic )
                $styleAttr .= " font-style: italic;";

            $result .= "<div style='" . $styleAttr . "'>" . $afterContentTab[ $i ] . "</div>";
        }

        $i++;
    }

    $result .= "</ul>";
    return $result;
}

$COMMON_PATH = get_stylesheet_directory_uri() . "/common";

get_header(); ?>

<!-- librairie pour gerer le nuage de mot -->
<link rel='stylesheet' type='text/css' href='<?php echo $COMMON_PATH . "/styles/jqcloud.css"; ?>' />
<script type='text/javascript' src='<?php echo $COMMON_PATH . "/scripts/jqcloud.js"; ?>' ></script>

<style>
    .entry-meta,  /*    Permet de cacher la date, l'heure et le nom de l'auteur de l'article, pour éviter les confusions avec les dates, heures et noms des conférenciers et des conférences */
    .entry-tags,  /*    Permet de cacher les etiquettes */
    .entry-footer, /*   Permet de cacher l'entete de navigation (sous la photo) avec les 2 hr */
    .jp-relatedposts /* Permet de cacher les posts similaires */ {
        display: none;
    }

    .cont_entete {
        margin-top: 1em;
        margin-bottom: 1em;
        height: 40px;
    }

    .pastilleLeft {
        margin-right: 1em;
    }

    .pastilleRight {
        margin-left: 1em;
    }

    .column-m150-left {
        float: left;
        width: 20%;
    }

    .column-m150-right {
        float: left;
        width: 78%;
    }

    .li-speaker_bio, .li-agenda {
        list-style-type: none;
    }

    .li-event_booking_url {
        list-style-type: none;
        font-style: italic;
    }

    .li-book_title {
        list-style-type: none;
        font-style: italic;
    }

    .li-speaker_bio:before {
        content: "- ";
    }

    .li-agenda:before {
        content: "* ";
    }

    .li-book_title:before {
        font-style: normal;
        content: "> \00AB  ";
    }

    .li-book_title:after {
        font-style: normal;
        content: " \00BB";
    }

    .li-event_booking_url:before {
        content: "* ";
    }

    .li-event_booking_url:after {
        content: " *";
    }

    .title-event {
        text-align: right;
    }
</style>

<section id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

    <?php while ( have_posts() ) : the_post();
        get_template_part( 'template-parts/content', 'single' );

        $event_type = get_field('event_type' );

        // Recupere les dates et heures + formatage
        $dtEvent      = get_field('start_date_time_event');
        $dtColorEvent = getDateTimeColor($event_type );
        $dtEndEvent   = get_field( 'end_date_time_event' );
        if ( $dtEndEvent != "") {
	        $dtEvent = "du " . $dtEvent . " au " . $dtEndEvent;
        }

        $dtEvent = "<span style='color: #" . $dtColorEvent . ";'>" . $dtEvent . "</span>";
        $pastille = getPastille("pastilleLeft" );
        $leftValue = $pastille . $dtEvent;

        $speakerValue = get_field('speaker_name' );
        $speakerRole =  get_field('speaker_role' );
        if ($speakerRole != "")
            $speakerValue .= ", " . $speakerRole;

        $speakerValue = "<span style='font-size: 1.3em;' >" . $speakerValue . "</span>";
        $rightValue = $speakerValue . getPastille("pastilleRight" );

        $bioValue = getHtmlList( 'speaker_bio' );

        $agendaValue = getHtmlList( 'agenda', 'agenda_info', true );

        $bookValue = getHtmlList("book_title", "book_info" );

        $urlContent = "";
        $urlValue = get_field( 'event_booking_url' );
        if ( $urlValue != "" ) {
            $urlContent = "<ul>";
            $urlContent .= "<li class=\"li-event_booking_url\"><a target=\"_blank\" href=\"$urlValue\">Cliquer ici pour réserver</a></li>";
            $urlContent .= "</ul>";
        }

        $title_event = get_field("event_title");
        if ( empty( $title_event ) )
            $title_event = get_the_title();

        $title_event = "<div class='title-event'><h4>&laquo; " . $title_event . " &raquo;</h4></div>";

        $tags = get_field("event_tags" );
        $tags = explode("\n", $tags);

        $jsCode = "";
        foreach ( $tags as $tag ) {
            if ( empty( $tag ))
                continue;

            if (! empty($jsCode)) {
                $jsCode .= ",";
            }

            $jsCode .= "{ text:'" . str_replace( "\r", "", $tag ) . "', weight: ". random_int( 1, 10 ) . " }";
        }
        ?>

        <script type="text/javascript">

            function drawFillCircle( canvasId ) {
                jQuery('#' + canvasId).attr({width:22,height:22}).css({width:'22px',height:'22px'});

                let canvas = document.getElementById(canvasId);
                let context = canvas.getContext('2d');
                let centerX = canvas.width / 2;
                let centerY = canvas.height / 2;
                let radius = 11;

                context.beginPath();
                context.arc(centerX, centerY, radius, 0, 2 * Math.PI, false);
                context.fillStyle = "#<?php echo $dtColorEvent ?>";
                context.fill();
            }

            jQuery(document).ready(function() {

                drawFillCircle("pastilleLeft");
                drawFillCircle("pastilleRight");

                // Gestion du nuage de mots
                let words = [ <?php echo $jsCode; ?> ];

                jQuery("#event_tags").jQCloud(words, {
                    height: 250,
                    autoResize: true,
                    colors: [ "#<?php echo $dtColorEvent ?>" ], // "#404040"
                    fontSize: [ 30, 25, 20 ]
                });

                jQuery('#column_right').resize(function () {
                    jQuery("#event_tags").width( jQuery("#column_right").width() );
                });

                jQuery("#jp-relatedposts").remove();
            });
        </script>

        <hr>
        <div id="event">
            <div class="cont_entete">
                <div style="float:left;" ><?php echo $leftValue; ?></div>
                <div style="float:right; text-align: right;"><?php echo $rightValue; ?></div>
            </div>
            <div id="cont_body">
                <div class="column-m150-left">
                    <div id="speaker_image"><?php echo getImgSrc( 'speaker_image' ); ?></div>
                    <div id="event_image1"><?php  echo getImgSrc( 'event_image1' ); ?></div>
                    <div id="event_image2"><?php  echo getImgSrc( 'event_image2' ); ?></div>
                </div>
                <div id="column_right" class="column-m150-right">
                    <div id="speaker_bio"><?php echo $bioValue; ?></div>
                    <div id="agenda"><?php echo $agendaValue; ?></div>
                    <div id="book_title"><?php echo $bookValue; ?></div>
                    <div id="event_tags"></div>
                    <div id="event_title"><?php echo $title_event; ?></div>
                    <br>
                    <div id="event_booking_url"><?php echo $urlContent; ?></div>
                </div>
            </div>
        </div>

        <?php
        // poseidon_related_posts();

        // comments_template();

    endwhile; ?>

    </main><!-- #main -->
</section><!-- #primary -->

<?php get_sidebar();

get_footer();
