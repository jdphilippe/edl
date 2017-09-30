<?php
//ini_set('display_errors',1);
//error_reporting(E_ALL);

    $parse_uri = explode( 'wp-content', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME') );
    require_once( $parse_uri[0] . 'wp-load.php' );

    // On efface les fichiers existants
    $jsonDir = dirname(__FILE__) . "/../json";
    if (is_dir($jsonDir)) {
        array_map('unlink', glob("$jsonDir/*.*"));
    } else {
        mkdir($jsonDir);
    }

    // Recherche des predications et des eb
    $categoriesStr = [ "etude-biblique", "predication" ];
    foreach ($categoriesStr as $cat_slug) {
        $args = get_category_by_slug($cat_slug);
        $sub_categories = get_categories(
                [ "child_of"   => $args->term_id,
                  "hide_empty" => 0 ] // Renvoie aussi les categories qui n'ont pas encore d'article
            );

        if (empty($sub_categories)) {
            $sub_categories[] = $args; // cas pour les predications
        }

        foreach ($sub_categories as $sub_cat) {
            generateJSONFile([ $sub_cat ], TRUE);
            generateJSONFile([ $sub_cat ], FALSE);
        }
    }
