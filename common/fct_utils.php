<?php

$COMMON_PATH = get_stylesheet_directory_uri() . '/common';

// ----------------------- Fonctions generiques -----------------------

function startsWith($haystack, $needle) {
    return ( strpos( $haystack, $needle ) === 0 );
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length === 0) {
        return true;
    }

    return ( substr($haystack, -$length) === $needle );
}

if (! function_exists('write_log'))
{
    function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

}

// ----------------------- Fonctions generiques -----------------------

function isSermon($post_id)
{
    $cats = get_the_category($post_id);
    $found = false;
    foreach ($cats as $c) {
        if ( $c->slug === 'predication' ) {
            $found = true;
            break;
        }
    }

    return $found;
}

/*
 * Recupere un extrait pour l'afficher dans une bulle tooltip
 */
function getExcerptOfSermon( $post_id )
{
    return ''; // Pas activé pour le moment

    // Uniquement valable pour les predications
    if (! isSermon($post_id)) {
        return '';
    }

    $the_post  = get_post($post_id);    // Gets post ID
    $result    = $the_post->post_content;
    $endString = '</span>';

    $sermonStart = strrpos($result, $endString);
    if ($sermonStart === false) {
        $sermonStart = 0;
    } else {
        $sermonStart += strlen($endString);
    }

    $arrayOfChars = ['?', '!', '.'];
    $maxChar = 300;
    $minChar = 200;
    if (strlen($result) > $sermonStart + $maxChar) {
    	$result = substr($result, $sermonStart, $maxChar);
        if ($result === false) {
            $result = 'probleme de chaine.';
        } else {
            for ($i = $maxChar; $i >= $minChar; $i--) {
                if ( in_array( $result[$i - 1], $arrayOfChars, true ) ) {
                    $result = substr($result, 0, $i);
                    break;
                }
            }
        }

        $result .= ' [...]';
    }

    $count = -1;
    $result = str_replace("'", '&#39;', $result, $count);
    return trim( $result );
    //return htmlspecialchars($result, ENT_QUOTES);
}

/*
function getExcerptById($post_id) {
    $the_post = get_post($post_id);    // Gets post ID
    $the_excerpt = $the_post->post_content; // Gets post_content to be used as a basis for the excerpt
    $excerpt_length = 35;                      // Sets excerpt length by word count
    $the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
    $words = explode(' ', $the_excerpt, $excerpt_length + 1);

    if (count($words) > $excerpt_length) {
        array_pop($words);
        array_push($words, '…');
        $the_excerpt = implode(' ', $words);
    }

    $the_excerpt = '<p>' . $the_excerpt . '</p>';
    return esc_html($the_excerpt);
}
*/

function surroundWithDiv($value, $center)
{
    if ( $value === '' ) {
        return '';
    }

    $result = "<div style='word-wrap: break-word; overflow: hidden; max-height: 52px"; // ne pas terminer par '
    if ($center) {
        $result .= " text-align:center; vertical-align:middle;' align='center";
    }

    $result .= "'>" . $value . '</div>';

    return $result;
}

// Pour rechercher les textes bibliques dans un post
function extract_text_from_tag( $attr, $value, $string )
{
    $attr  = preg_quote($attr, null);
    $value = preg_quote($value, null);
    $pattern = '/<span[^>]*' . $attr . '="' . $value . '">(.*?)<\\/span>/si';
    $matches = [];
    $matchcount = preg_match_all($pattern, $string, $matches);

    $result = '';
    for ($i = 0; $i < $matchcount; $i++) {
        if (strlen( $result) > 0) {
            $result .= '<br/>';
        }

        // Enleve un eventuel <br />\n en debut de chaine (la ref biblique commence par un retour a la ligne)
        $tmp = strip_tags($matches[1][ $i ]);
        $tmp = trim( $tmp );
        $result .= $tmp;
    }

    return $result;
}

// Pour extraire et creer les liens vers les fichiers mp3

function createLink( $link )
{
    global $COMMON_PATH;

    $image = '';
    $text  = '';
    $filename = basename($link);
    if (endsWith($link, 'debout-sainte-cohorte.mp3' )) {
	    return ''; // On ignore les mp3 ajoutes sur les cultes
    }

	if (endsWith($link, 'pdf' )) {
        $image = 'pdf.png';
        $text  = 'Lire';
    } else if (endsWith($link, 'mp3' )) {
        $image = 'button_play.png'; // Pour les EB
        $text  = 'Ecouter';         // Pour les EB

        if ( (strpos($link, 'predic') !== false) || (strpos($link, 'méditation') !== false) ) {
            $image = 'button_play_predic.png';
            $text .= ' la prédication';
        } else if (strpos($link, 'culte') !== false) {
            $text .= ' le culte';
        }
    } else if ( strpos($link, 'youtu' ) !== false) {
		$filename = '';
        $image = 'tv.png';
        $text  = 'Regarder la prédication';
        if ( strpos($link, 'youtu.be' ) !== false) {
            // On remplace les liens abreges par des versions longues, car je rencontre des erreurs 404 avec
            $lastSlash = strrpos($link, '/' );
            $endURL = substr($link, $lastSlash + 1);
            $link = 'https://www.youtube.com/watch?v=' . $endURL;
        }
    }

    if ( $image === '' ) {
        return '';
    }

    if (startsWith($link, 'http:' )) {
        $link = substr_replace($link, 's', 4, 0); // Pour forcer les liens en HTTPS
    }

    $href = "<a href='" . $link . "' target='_blank'";
    if ( $filename !== '' ) {
	    $href .= " download='" . $filename . "'";
    }

    return $href . "><img src='" . $COMMON_PATH . '/images/' . $image . "' style='vertical-align:middle;' alt='" . $text . "' title='" . $text . "'/></a>";
}

function isVideo( $media )
{
	return strpos( $media, 'youtu' ) !== false;
}

function findMedia( $post, $isMobile, $isGuest )
{
    $result = '';
    $tab    = array();

    // Recherche en passant par les API WP

    $media = get_attached_media('audio', $post->ID);
    /*
    foreach ($media as $audio)
    {
        $tab[] = createLink(wp_get_attachment_url($audio->ID));
    }
    */

    // Recherche egalement par expression reguliere dans le texte de l'article
    $content = $post->post_content;
    preg_match_all('/<a[^>]+href=([\'"])(.+?)\1[^>]*>/i', $content, $media);
    if (! empty($media))
    {
        $media = array_unique($media[2]); // Peut y avoir des liens en double. Balise <a href> et [audio]
        foreach ($media as $m)
        {
            if ( $isGuest && strpos($m, 'culte') !== false )
                continue;

            $link = createLink($m);
            if ( $link !== '' && ! isVideo( $link ))
            {
		        $tab[] = $link;
            }
        }
    }

	$hasVideo = false;
    $content = trim($content);
    if (startsWith($content, '[youtube ' )) {
        $link = substr( $content, 9, strpos( $content, ']' ) -9 );
        $link = createLink($link);
        $tab[] = $link;
        $hasVideo = true;
    }

    if ( empty($tab) ) {
        return '';
    }

    $tab = array_unique($tab);
    asort($tab);

    if ( ! $isMobile && isSermon( $post->ID ) )
    {
	    $dummyLink = '<img src="https://espritdeliberte.leswoody.net/wp-content/uploads/2018/07/img_transparente.png" style="vertical-align: middle;"  alt="">';

        if (! $isGuest)
        {
            switch ( count( $tab ) )
            {
                case 1:
                    if ( ! strpos($tab[0], 'culte') !== false ) {
                        array_splice( $tab, 0, 0, $dummyLink ); // On place ce lien en premier
                    }
                    break;

                case 2:
                    if ( $hasVideo ) {
                        array_splice( $tab, 0, 0, $dummyLink ); // On place ce lien en premier
                    }
                    break;

                default :
            }
        }
        else
        {
            if ( $hasVideo && count( $tab ) == 1 )
            {
                array_splice( $tab, 0, 0, $dummyLink ); // On place ce lien en premier
            }
        }
    }

    foreach ( $tab as $mp3 ) {
        $result .= ' ' . $mp3;
    }

    return $result;
}

function isCategoryParentOf( $cat_ID, $parentCat ) {
    $slug = get_the_category_by_ID( $cat_ID );
    if ( $slug === $parentCat ) {
        return true;
    }

    $tab = get_ancestors( $cat_ID, 'category' );

    return empty( $tab ) ? false : isCategoryParentOf( reset($tab), $parentCat );
}

function isJSONable( $cat_ID ) {
	return isCategoryParentOf( $cat_ID, 'jsonable' );
}

function get_last_category_child( $cat_ID ) {
    $result = $cat_ID;
    $tab = category_has_children( $cat_ID );
    if ( empty( $tab )) {
        return $result;
    }

    return get_last_category_child( reset($result) );
}

function category_has_children( $term_id = 0, $taxonomy = 'category' ) {
    return get_categories( array(
        'child_of'      => $term_id,
        'taxonomy'      => $taxonomy,
        'hide_empty'    => false,
        'fields'        => 'ids',
    ));
}
