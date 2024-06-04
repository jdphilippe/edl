<?php

if (WP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

require_once __DIR__ . '/common/fct_utils.php';
require_once __DIR__ . '/common/date_utils.php';

/*
 *  activation theme
 */
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');

function theme_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}

/*
function exclude_category( $query ) {
	if ( $query->is_home() ) {
	    $m150_cat = get_category_by_slug('maguelone150ans');
		$query->set( 'cat', '-' . $m150_cat->term_id );
	}
	return $query;
}
add_filter( 'pre_get_posts', 'exclude_category' );
*/

// Affiche la case a cocher "Ne pas envoyer d'email"
add_filter('jetpack_allow_per_post_subscriptions', '__return_true');

function php_execute($html)
{
    if (strpos($html, "<" . "?php") !== false) {
        ob_start();
        eval("?" . ">" . $html);
        $html = ob_get_contents();
        ob_end_clean();
    }

    return $html;
}

add_filter('widget_text', 'php_execute');

/*
* On utilise une fonction pour créer notre custom post type 'maguelone150ans'
*/

function pjd_add_post_type_m150ans()
{

    $labels = array(
        // Le nom au pluriel
        'name'                => _x('Evénements - Maguelone 150 ans', 'Post Type General Name'),
        // Le nom au singulier
        'singular_name'       => _x('Evénement 150 ans', 'Post Type Singular Name'),
        // Le libellé affiché dans le menu
        'menu_name'           => __('Maguelone 150 ans'),

        // Les différents libellés de l'administration
        'all_items'           => __('Tous les événements'),
        'view_item'           => __('Voir les événements'),
        'add_new_item'        => __('Ajouter un nouvel événement'),
        'add_new'             => __('Ajouter'),
        'edit_item'           => __("Editer l'événement"),
        'update_item'         => __("Modifier l'événement"),
        'search_items'        => __('Rechercher un événement'),
        'not_found'           => __('Non trouvé'),
        'not_found_in_trash'  => __('Non trouvé dans la corbeille'),
    );

    $args = array(
        'label'               => __('Maguelone 150 ans'),
        'description'         => __('Tout sur Maguelone 150 ans'),
        'labels'              => $labels,
        // On définit les options disponibles dans l'éditeur de notre custom post type ( un titre, un auteur...)
        'supports'            => array('title', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
        /*
		* Différentes options supplémentaires
		*/
        'show_in_rest'        => true,
        'hierarchical'        => false,
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => array('slug' => 'maguelone-150-ans')
    );

    // On enregistre notre custom post type qu'on nomme ici "maguelone150ans" et ses arguments
    register_post_type('maguelone150ans', $args);
}
add_action('init', 'pjd_add_post_type_m150ans', 0);

function pjd_add_post_type_external_post()
{

    $labels = array(
        // Le nom au pluriel
        'name'                => _x('Articles externes', 'Post Type General Name'),
        // Le nom au singulier
        'singular_name'       => _x('Article externe', 'Post Type Singular Name'),
        // Le libellé affiché dans le menu
        'menu_name'           => __('Articles externes'),

        // Les différents libellés de l'administration
        'all_items'           => __('Tous les articles'),
        'view_item'           => __('Voir les articles'),
        'add_new_item'        => __('Ajouter un nouvel article'),
        'add_new'             => __('Ajouter'),
        'edit_item'           => __("Editer l'article"),
        'update_item'         => __("Modifier l'article"),
        'search_items'        => __('Rechercher un article'),
        'not_found'           => __('Non trouvé'),
        'not_found_in_trash'  => __('Non trouvé dans la corbeille'),
    );

    $args = array(
        'label'               => __('Articles externes'),
        'description'         => __('Tous les articles externes'),
        'labels'              => $labels,

        // On définit les options disponibles dans l'éditeur de notre custom post type ( un titre, un auteur...)
        'supports'            => array('title', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),

        /*
		* Différentes options supplémentaires
		*/
        'show_in_rest'        => true,
        'hierarchical'        => false,
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => array('slug' => 'article-externe')
    );

    // On enregistre notre custom post type qu'on nomme ici "article-externe" et ses arguments
    register_post_type('article-externe', $args);
}
add_action('init', 'pjd_add_post_type_external_post', 0);

add_action('pre_get_posts', 'add_external_post_type_to_query');

/**
 *  Permet d'ajouter les articles externes dans la home page
 *
 * @param $query
 *
 * @return mixed
 */
function add_external_post_type_to_query($query)
{

    if (is_home() && $query->is_main_query()) {
        $query->set('post_type', array('post', 'article-externe'));
    }

    return $query;
}


function getEventColor_m150ans($event_type)
{

    $result = '';
    switch ($event_type) {
        case 'concert':
            $result = '41ae4a';
            break;

        case 'conference':
            $result = '0056ee';
            break;

        case 'theologie':
            $result = 'd2242b';
            break;
    }

    return $result;
}

function poseidon_footer_text_edl()
{
?>

    <table class="no-border">
        <tr>
            <td>
                <a href="https://espritdeliberte.leswoody.net/contact/" target="_blank">Contact</a><br />
                <!-- Pour faire un don, suivez <a href="https://espritdeliberte.leswoody.net/donner" target="_blank">ce lien</a>. -->
            </td>
            <td>
                Copyright &copy; 2017-<?php echo date('Y') ?>, tous droits réservés.<br />
                Propulsé par <a href="http://wordpress.org" title="WordPress">WordPress</a> et <a href="https://themezee.com/themes/poseidon/" title="Poseidon WordPress Theme">Poseidon</a>.
            </td>
            <td>
                Si vous voulez nous faire part d'une suggestion<br> ou signaler un problème, écrivez à <a href="mailto:webmaster@leswoody.net" title="Envoyer un email">webmaster @ leswoody.net</a>.
            </td>
        </tr>
    </table>

<?php
}
add_action('poseidon_footer_text', 'poseidon_footer_text_edl');

function my_child_theme_locale()
{
    load_child_theme_textdomain('poseidon', get_stylesheet_directory() . '/languages');
}
add_action('after_setup_theme', 'my_child_theme_locale');


// Pour desactiver le onclick sur les menus parent
function jqueryscript_in_head()
{ ?>
    <script type="text/javascript">
        let $j = jQuery.noConflict();
        $j(document).ready(function() {
            $j("li:has(ul)").children("a").click(function() {
                return false;
            });
        });
    </script>
<?php }
add_action('wp_head', 'jqueryscript_in_head');


// Cette partie concerne l'ajout du champ "Profession" dans les profile-card des utilisateurs
/*
function addUserOccupationFields($user) {
    ?>
    <script type="text/javascript">
        function addSample() {
            let sample = "Titre d'une predication, par le " + jQuery("#occupation").val() + " " + jQuery('#first_name').val() + " " + jQuery('#last_name').val();
            jQuery("#exemple").html(sample);
        }

        jQuery(document).ready(function () {
            addSample();
        });
    </script>

    <h3>Autres: </h3>
    <table class='form-table'>
        <tr>
            <th>Profession</th>
            <td><input type='text' name='occupation' id='occupation' onkeyup='addSample()' onchange='addSample()' onblur='addSample()'
                       value='<?php echo esc_attr(get_the_author_meta('occupation', $user->ID)); ?>' />

                <p class='description'>
                    La profession apparaîtra à la fin du titre de la prédication. (Pasteur, Professeur, etc.)<br>
                    <span id="exemple"></span>
                </p>
            </td>
        </tr>
    </table>
    <?php
}

add_action('show_user_profile', 'addUserOccupationFields');
add_action('edit_user_profile', 'addUserOccupationFields');
add_action('user_new_form'    , 'addUserOccupationFields');

function saveUserOccupationFields($user_id) {
    if (!current_user_can('edit_user')) {
        return ;
    }

    update_user_meta($user_id, 'occupation', filter_input(INPUT_POST, 'occupation')); // $_POST['occupation']);
}

add_action('personal_options_update',  'saveUserOccupationFields');
add_action('edit_user_profile_update', 'saveUserOccupationFields');
*/

function get_user_role($id)
{
    $user = new WP_User($id);
    return $user->roles[0];
}

// Pour enlever les articles privés de la Home Page, meme si on est logue admin
function my_private_post_filter($where = '')
{
    // Make sure this only applies to loops / feeds on the frontend
    if (!is_single() && !is_admin()) {
        // exclu les articles privees
        $where = str_replace("OR wppr_posts.post_status = 'private'", '', $where);
    }

    return $where;
}
add_filter('posts_where', 'my_private_post_filter');

// Enleve les lettres Privé: des titres des articles privés (EB non encore publiée)
function trim_title($title)
{

    $title = esc_attr($title);

    if (startsWith($title, 'Privé')) {
        $title = substr($title, strpos($title, ':') + 1); // +1 blanc
    }

    return $title;
}
add_filter('the_title', 'trim_title');

/*
function modify_sermon_title($post_id)
{
    $isSermon = false;
    $categories = get_the_category($post_id);
    foreach ($categories as $category) {
        if ($category->slug === 'predication') {
            $isSermon = true;
            break;
        }
    }

    if (!$isSermon) {
        return;
    }

    $post = get_post($post_id);
    if (get_user_role($post->post_author) !== 'author') {
        return;
    }

    $the_author     = get_the_author_meta('display_name', $post->post_author);
    $the_occupation = get_the_author_meta('occupation', $post->post_author);
    $title = get_the_title($post->ID);
    $endTitle = ', par le ' . $the_occupation . ' ' . $the_author;

    if (endsWith($title, $endTitle)) {
        return;
    }

    $title .= $endTitle;
    $my_post = [
        'ID' => $post->ID,
        'post_title' => $title
    ];

    wp_update_post($my_post);
}

add_action('save_post', 'modify_sermon_title');
*/

function generateJSONFiles($post_id)
{
    $categories = get_the_category($post_id);

    // On ne prend que les articles jsonable
    foreach ($categories as $category) {
        if (isJSONable($category->term_id)) {
            if (empty(category_has_children($category->term_id))) {
                generateJSONFilesFromCategory($category);
            } else {
                // Erreur de selection, on ne doit selectionner que les elements feuilles de la hierarchie 'jsonable',
                // pour eviter leur affichage dans le widget 'categorie'
                wp_remove_object_terms($post_id, $category->term_id, 'category');
            }
        }
    }
}

add_action('save_post', 'generateJSONFiles');

function generateJSONFilesFromCategory($jsonableCategory)
{

    // Config pour les EB & KT
    $postStatus = 'any';
    $orderType  = 'ASC';
    $category   = $jsonableCategory->slug;

    if ($category === 'predication') {
        $postStatus = 'publish';
        $orderType  = 'DESC';
    }

    generateJSONFile([$category], $orderType, $postStatus, true);
    generateJSONFile([$category], $orderType, $postStatus, false);
}

/**
 * La fonction raccourcirChaine() permet de réduire une chaine trop longue
 * passée en paramètre.
 *
 * Si la troncature a lieu dans un mot, la fonction tronque à l'espace suivant.
 *
 * @param : string $chaine le texte trop long à tronquer
 * @param : integer $tailleMax la taille maximale de la chaine tronquée
 * @return : string
 */
function raccourcirChaine($chaine, $tailleMax)
{

    if (strlen($chaine) > $tailleMax) {
        $chaine = substr($chaine, 0, $tailleMax);
        $positionDernierEspace = strrpos($chaine, ' ');
        $chaine = substr($chaine, 0, $positionDernierEspace) . ' [...]';
    }

    return $chaine;
}

function generateJSONFile($categories, $orderType, $postStatus, $isMobile)
{
    try {
        $criteria = [
            'post_type'      => 'post',
            'tax_query'      => [
                [
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $categories
                ]
            ],
            'orderby'        => 'post_date',
            'order'          => $orderType,
            'post_status'    => $postStatus,
            'posts_per_page' => '-1'
        ];

        // Lancement de la recherche
        $the_query = new WP_Query($criteria);
        $data   = [];
        $width  = 78;
        $height = 52;
        $paddingTop = 14;
        $paddingLeft = 10;
        $dateUtils = new DateUtils();

        // Parcours des articles trouves
        foreach ($the_query->posts as $post) {
            $content = apply_filters('the_content', $post->post_content);
            $biblicalRef = extract_text_from_tag('class', 'ref-biblique', $content);

            $media   = '';
            $tooltip = '';
            $title = get_the_title($post);
            $isPublished = get_post_status($post) === 'publish';
            if (!$isMobile) {
                $image = get_the_post_thumbnail_url($post, [$width, $height]);

                // On met l'image par defaut si non definie
                // Concerne les EB annoncées, mais non publiées
                if ($image === null || $image === false) {
                    $image = $isPublished ? 'https://espritdeliberte.leswoody.net/wp-content/uploads/2017/08/v6.png' :
                        'https://espritdeliberte.leswoody.net/wp-content/uploads/2017/08/vide.png';
                }

                $tooltip = $title;

                // Si le titre est trop long, on garde que les 110 premier caractères et on ajoute [...]
                $title = raccourcirChaine($title, 110);

                // On place sur la gauche du titre l'image de l'article
                $title =
                    "<div style='overflow: hidden; max-height: " . $height . "px; float: left' >" .
                    "<img src='" . $image . "' width='" . $width . "px' alt='" . $tooltip . "' />" .
                    '</div>' .
                    "<div style='word-wrap: break-word; padding-top: " . $paddingTop . 'px; padding-left: ' . $paddingLeft . "px; display: inline-flex' >" .
                    $title .
                    '</div>';
            }

            // Affiche un lien si l'article est publie
            if ($isPublished) {
                $title = "<a href='" . get_post_permalink($post->ID) . "' target='_blank' title='" . $tooltip . "'>" . $title . '</a>';
                $media = findMedia($post, $isMobile);
            }

            $formatDate  = $isMobile ? 'd/m' : 'd/m/Y';
            $displayDate = get_the_time($formatDate, $post->ID);
            $timestamp   = get_the_time('G', $post->ID);

            if (!$isMobile && in_array('predication', $categories, true)) {
                $comment = $dateUtils->getComment($timestamp);
                if ($comment !== '') {
                    $displayDate .= '<br><span style="color: chocolate;">' . $comment . '</span>';
                }
            }

            $data[] = [
                'date'   => [
                    'display'   => surroundWithDiv($displayDate, true),
                    'timestamp' => $timestamp
                ],
                'title'  => $title,
                'link'   => surroundWithDiv($media, false),
                'refbib' => surroundWithDiv($biblicalRef, false)
            ];
        }

        $recordsTotal = $the_query->found_posts;

        // Restore original Post Data
        wp_reset_postdata();

        $response = [
            'draw'         => (int) $recordsTotal,
            'recordsTotal' => $recordsTotal,
            'data'         => $data
        ];

        $json = json_encode($response);
        $fname = __DIR__ . '/json/' . implode('-', $categories);
        if ($isMobile) {
            $fname .= '_mobile';
        }

        $fname .= '.json';

        $fp = fopen($fname, 'wb');
        if (!$fp) {
            echo "Erreur d'acces au disque !!!";
            return;
        }

        fwrite($fp, $json);
        fclose($fp);
    } catch (Exception $e) {
        var_dump('Exception reçue : ' . $e->getMessage());
    }
}

function getLastEB() // Appele directement depuis un widget texte
{
    // the query
    $the_query = new WP_Query(array(
        'category_name'  => 'etude-biblique',
        'orderby'        => 'post_date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
        'posts_per_page' => '1'
    ));

    // The Loop
    if (! $the_query->have_posts()) {
        wp_reset_postdata();
        return '<li>No Posts Found</li>';
    }

    $string = '<p style="margin-left: 10px;">';
    $post = $the_query->get_posts()[0];

    $url       = get_post_permalink($post->ID);
    $tooltip   = get_the_title($post);
    $image     = get_the_post_thumbnail_url($post);
    $categorie = get_the_category($post->ID)[0];

    // Suppression du texte entre (), s'il y en a. Par ex: Notre pere (Catechisme) -> Notre Pere
    $pos = strpos($categorie->name, "(");
    if ($pos !== false) {
        $categorie->name = substr($categorie->name, 0, $pos -1);
    }

    $string .= '<a href="' . $url . '" rel="bookmark" target="_blank" >';
    $string .= '<span class="entry-header"><b>' . $tooltip . '</b></span>';
    $string .= '<img src="' . $image . '" >';
    $string .= '</a>';
    $string .= '</p>';
    $string .= '<p style="margin-left: 10px;">';
    $string .= "Cycle de l'année: ";
    $string .= '<a href="https://espritdeliberte.leswoody.net/etude-biblique" target="_blank"><i>' . $categorie->name . '</i></a>';
    $string .= '</p>';

    /* Restore original Post Data */
    wp_reset_postdata();

    return $string;
}

function wpdf_redirect_par_wp()
{
    global $wp;

    if (!is_404()) {
        return;
    }

    // Pour ne pas casser les anciens liens
    // Anciennes URL  =>   Nouvelles URL
    $wpdf_liste_redirections = [
        '/2016/12/24/contact'     => 'contact',           // Premiere page contact, il y a des liens dans FB
        '/liste-des-predications' => 'page-des-cultes',
        '/etudes-bibliques'       => 'etude-biblique',    // 06/10/2017
        '/wp-content/uploads/2019/12/LIVRET-150-ANS-FINALISE-low.pdf' => 'maguelone-150-ans',
        '/2019/12/17/au-programme-des-150-ans-du-temple-de-maguelone' => 'maguelone-150-ans',
        '/donner'  => 'https://espritdeliberte.leswoody.net/2023/12/21/un-don-pour-soutenir-nos-actions/' //'https://epudf-montpellier.s2.yapla.com/fr/don/donate/soutenir-leglise-protestante-unie-de-montpellier/3344/',
    ];

    // rechercher les redirections génériques
    $wpdf_explode_request = explode('/', $wp->request); // Split en tableau
    $wpdf_nouvelle_adresse = ''; // variable qui va contenir la nouvelle adresse
    $wpdf_modif_adresse = false; // indique si une redirection a été trouvée dans le tableau

    // Ajout d'un patch pour corriger les liens de la lettre Mailship pour Maguelone 150 ans
    /*
    if (  $wpdf_explode_request[0] === '2019' && $wpdf_explode_request[1] === '12' ) {
	    $wpdf_explode_request[2] = '17';
	    $wpdf_nouvelle_adresse = '/' . implode( '/', $wpdf_explode_request );
	    $wpdf_modif_adresse = true;
    }*/

    //if (! $wpdf_modif_adresse) {
    foreach ($wpdf_explode_request as $wpdf_elt_tableau) {
        $old_url = $wpdf_nouvelle_adresse . '/' . $wpdf_elt_tableau . '*';

        if (array_key_exists($old_url, $wpdf_liste_redirections)) {
            $wpdf_modif_adresse    = true; // une redirection a été trouvée
            $wpdf_nouvelle_adresse = '/' . $wpdf_liste_redirections[$old_url]; // l'élément doit être remplacé dans la nouvelle adresse
        } else {
            // si pas trouvé dans le tableau, l'élément doit être conservé dans la nouvelle adresse
            $wpdf_nouvelle_adresse .= '/' . $wpdf_elt_tableau;
        }
    }

    if (array_key_exists($wpdf_nouvelle_adresse, $wpdf_liste_redirections)) {
        $wpdf_modif_adresse    = true; // une redirection a été trouvée
        $wpdf_nouvelle_adresse =  $wpdf_liste_redirections[$wpdf_nouvelle_adresse];
        if (filter_var($wpdf_nouvelle_adresse, FILTER_VALIDATE_URL) === FALSE) {
            $wpdf_nouvelle_adresse = '/' . $wpdf_nouvelle_adresse;
        }
    }
    //}

    if (filter_var($wpdf_nouvelle_adresse, FILTER_VALIDATE_URL) !== FALSE) {
        wp_redirect(esc_url($wpdf_nouvelle_adresse), 301);
    } else {
        $wpdf_domaine_site_cible = site_url(); // adresse domaine cible
        $query_string = filter_input(INPUT_SERVER, 'QUERY_STRING');
        $wpdf_param_url = ($query_string !== '') ? '?' . $query_string : ''; //	récupérer le paramétrage de l'url : "?clé = valeur"
        $nouvelle_adresse = $wpdf_modif_adresse ? $wpdf_domaine_site_cible . $wpdf_nouvelle_adresse . '/' . $wpdf_param_url : $wpdf_domaine_site_cible;
        wp_redirect(esc_url($nouvelle_adresse), 301);
    }

    exit; // toujours ajouter "exit" après une redirection
}

add_action('template_redirect', 'wpdf_redirect_par_wp');


// -----------------------------------------------------------------------------
//                   FONCTIONS D'ADMIN
// -----------------------------------------------------------------------------


/** Step 1. */
function my_esprit_de_liberte_menu()
{
    add_management_page('Esprit de Liberté - Outils JSON', 'Esprit de Liberté - JSON', 'manage_options', 'outils-esprit-libre', 'plugin_esprit_de_liberte_outils');
}

/** Step 2 (from text above). */
add_action('admin_menu', 'my_esprit_de_liberte_menu');

/** Step 3. */
function plugin_esprit_de_liberte_outils()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    echo "<iframe src='" . get_stylesheet_directory_uri() . "/admin/index.php" . "' width='1000' heigth='500' />";
}
