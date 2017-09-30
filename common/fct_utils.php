<?php

$COMMON_PATH = get_stylesheet_directory_uri() . "/common";

// ----------------------- Fonctions generiques -----------------------

function startsWith($haystack, $needle) {
    $length = strlen($needle);
    return ( substr($haystack, 0, $length) === $needle );
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return ( substr($haystack, -$length) === $needle );
}

// ----------------------- Fonctions generiques -----------------------

function isSermon($post_id) {
    $cats = get_the_category($post_id);
    $found = false;
    foreach ($cats as $c) {
        if ($c->slug === "predication") {
            $found = true;
            break;
        }
    }

    return $found;
}

/*
 * Recupere un extrait pour l'afficher dans une bulle tooltip
 */
function getExcerptOfSermon( $post_id ) {

    return ""; // Pas activé pour le moment

    // Uniquement valable pour les predications
    if (! isSermon($post_id)) {
        return "";
    }

    $the_post  = get_post($post_id);    // Gets post ID
    $result    = $the_post->post_content;
    $endString = "</span>";

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
            $result = "probleme de chaine.";
        } else {
            for ($i = $maxChar; $i >= $minChar; $i--) {
                if (in_array($result{$i -1}, $arrayOfChars)) {
                    $result = substr($result, 0, $i);
                    break;
                }
            }
        }

        $result .= " [...]";
    }

    $count = -1;
    $result = str_replace("'", "&#39;", $result, $count);
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

function surroundWithDiv($value, $center) {
    if ($value == "") {
        return "";
    }

    $result = "<div style='word-wrap: break-word; overflow: hidden; max-height: 52px"; // ne pas terminer par '
    if ($center) {
        $result .= " text-align:center; vertical-align:middle;' align='center";
    }

    $result .= "'>" . $value . "</div>";

    return $result;
}

// Pour rechercher les textes bibliques dans un post
function extract_text_from_tag($attr, $value, $string) {
    $attr  = preg_quote($attr);
    $value = preg_quote($value);
    $pattern = '/<span[^>]*' . $attr . '="' . $value . '">(.*?)<\\/span>/si';
    $matches = [];
    $matchcount = preg_match_all($pattern, $string, $matches);

    $result = "";
    for ($i = 0; $i < $matchcount; $i++) {
        if ($i >= 1) {
            $result = $result . "<br/>";
        }

        $result = $result . $matches[1][$i];
    }

    return $result;
}

// Pour extraire et creer les liens vers les fichiers mp3

function createLink($link) {
    global $COMMON_PATH;

    $image = "";
    $text  = "";
    if (endsWith($link, "pdf")) {
        $image = "pdf.png";
        $text  = "Lire";
    } else if (endsWith($link, "mp3")) {
        $image = "button_play.png"; // Pour les EB
        $text  = "Ecouter";         // Pour les EB

        if (strpos($link, 'predic') !== false) {
            $image = "button_play_predic.png";
            $text .= " la prédication";
        } else if (strpos($link, 'culte') !== false) {
            $text .= " le culte";
        }
    } else if (strpos($link, "youtu") !== false) {
        $image = "tv.png";
        $text  = "Regarder la prédication";
        if (strpos($link, "youtu.be") !== false) {
            // On remplace les liens abreges par des versions longues, car je rencontre des erreurs 404 avec
            $lastSlash = strrpos($link, "/");
            $endURL = substr($link, $lastSlash + 1);
            $link = "https://www.youtube.com/watch?v=" . $endURL;
        }
    }

    if ($image == "") {
        return "";
    }

    if (startsWith($link, "http:")) {
        $link = substr_replace($link, "s", 4, 0); // Pour forcer les liens en HTTPS
    }

    return "<a href='" . $link . "' target='_blank'><img src='" . $COMMON_PATH . "/images/" . $image . "' style='vertical-align:middle;' alt='" . $text . "' title='" . $text . "'/></a>";
}

function findMedia($post) {
    $result = "";
    $tab    = array();

    // Recherche en passant par les API WP

    $media = get_attached_media('audio', $post->ID);
    foreach ($media as $audio) {
        $tab[] = createLink(wp_get_attachment_url($audio->ID));
    }

    // Recherche egalement par expression reguliere dans le texte de l'article
    $content = $post->post_content;
    preg_match_all('/<a[^>]+href=([\'"])(.+?)\1[^>]*>/i', $content, $media);
    if (! empty($media)) {
        $media = array_unique($media[2]); // Peut y avoir des liens en double. Balise <a href> et [audio]
        foreach ($media as $m) {
            $link = createLink($m);
            if ($link != "") {
                $tab[] = $link;
            }
        }
    }

    $content = trim($content);
    if (startsWith($content, "[youtube ")) {
        $link = substr( $content, 9, strpos( $content, ']' ) -9 );
        $link = createLink($link);
        $tab[] = $link;
//        array_splice($tab, 0, 0, $link); // On met le lien youtube en premier
    }

    if (empty($tab)) {
        return "";
    }

    $tab = array_unique($tab);
    asort($tab);

    if ( isSermon($post->ID) && count($tab) === 1 ) {
        $tab[0] = surroundWithDiv($tab[0], true);
    }

    foreach ($tab as $mp3) {
        $result .= " " . $mp3;
    }

    return $result;
}