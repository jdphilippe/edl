<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $parse_uri = explode( 'wp-content', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME') );
    require_once( $parse_uri[0] . 'wp-load.php' );

    // On efface les fichiers existants
    $jsonDir = dirname(__FILE__) . "/../json";
    if (is_dir($jsonDir)) {
        array_map('unlink', glob("$jsonDir/*.*"));
    } else {
        mkdir($jsonDir);
    }

    // Recherche des categories heritant de la categorie jsonable

    $term_id = get_category_by_slug( 'jsonable' )->cat_ID;
    $taxonomy_name = 'category';
    $term_children = get_term_children( $term_id, $taxonomy_name );

    foreach ($term_children as $cat_ID) {
        $category = get_term($cat_ID, 'category');
        if (empty ( category_has_children( $cat_ID ) )) {
            generateJSONFilesFromCategory($category);
        }
    }
