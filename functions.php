<?php
require_once dirname(__FILE__) . "/common/fct_utils.php";

/*
 *  activation theme
 */
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');

function theme_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}

function poseidon_footer_text_pjd() {
    ?>

    <table class="no-border">
        <tr>
            <td>
                <a href="https://espritdeliberte.leswoody.net/contact/" target="_blank">Contact</a><br/>
                Pour faire un don en ligne suivez <a href="https://www.eglise-protestante-unie.fr/montpellier-p20217/don" target="_blank">ce lien</a>.
            </td>
            <td>
                &copy; Copyright 2017, tous droits réservés.<br/>
                Fièrement propulsé par <a href="http://wordpress.org" title="WordPress">WordPress</a> et <a href="https://themezee.com/themes/poseidon/" title="Poseidon WordPress Theme">Poseidon</a>.
            </td>
            <td>
                Si vous voulez nous faire part d'une suggestion ou signaler un problème, écrivez à <a href="mailto:webmaster@leswoody.net" title="Envoyer un email">webmaster @ leswoody.net</a>.
            </td>
        </tr>
    </table>

    <?php
}

add_action('poseidon_footer_text', 'poseidon_footer_text_pjd');

function my_child_theme_locale() {
    load_child_theme_textdomain( 'poseidon', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'my_child_theme_locale' );


// Pour desactiver le onclick sur les menus parent
function jqueryscript_in_head() { ?>
    <script type="text/javascript">
        var $j = jQuery.noConflict();
        $j(document).ready(function() {
            $j("li:has(ul)").children("a").click(function () {
                return false;
            });
        });
    </script>
<?php }
add_action('wp_head', 'jqueryscript_in_head');

/*
$debug_tags = array();
add_action( 'all', function ( $tag ) {
    global $debug_tags;
    if ( in_array( $tag, $debug_tags ) ) {
        return;
    }
    echo "<pre>" . $tag . "</pre>";
    $debug_tags[] = $tag;
} );
*/

// Cette partie concerne l'ajout du champ "Profession" dans les profile-card des utilisateurs

function addUserOccupationFields($user) {
    ?>
    <script type="text/javascript">
        function addSample() {
            var sample = "Titre d'une predication, par le " + jQuery("#occupation").val() + " " + jQuery('#first_name').val() + " " + jQuery('#last_name').val();
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
            <td><input type='text' name='occupation' id='occupation' onkeyup='javascript:addSample()' onchange='javascript:addSample()' onblur='javascript:addSample()'
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
        return false;
    }

    update_user_meta($user_id, 'occupation', filter_input(INPUT_POST, 'occupation')); // $_POST['occupation']);
}

add_action('personal_options_update',  'saveUserOccupationFields');
add_action('edit_user_profile_update', 'saveUserOccupationFields');

function get_user_role($id) {
    $user = new WP_User($id);
    return $user->roles[0];
}

function modify_sermon_title($post_id) {
    $isSermon = false;
    $categories = get_the_category($post_id);
    foreach ($categories as $category) {
        if ($category->slug == "predication") {
            $isSermon = true;
            break;
        }
    }

    if (!$isSermon) {
        return;
    }

    $post = get_post($post_id);
    if (get_user_role($post->post_author) != "author") {
        return;
    }

    $the_author     = get_the_author_meta('display_name', $post->post_author);
    $the_occupation = get_the_author_meta('occupation'  , $post->post_author);
    $title = get_the_title($post->ID);
    $endTitle = ", par le " . $the_occupation . " " . $the_author;

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

function generateJSONFiles($post_id) {
    $categories = get_the_category($post_id);

    // On ne prend que les predication ou les etude bibliques
    $valid_cat = [ "predication", "etude-biblique" ];
    $found = false;
    foreach ($categories as $category) {
        if (in_array($category->slug, $valid_cat )) {
            $found = true;
            break;
        }
    }

    if (! $found ) {
        return;
    }

    generateJSONFile($categories, true);
    generateJSONFile($categories, false);
}

add_action('save_post', 'generateJSONFiles'); // 'publish_post'

function generateJSONFile($categories, $isMobile) {
    try {
        // Config pour les EB
        $postStatus = "any";
        $orderType  = "ASC";
        $tmp = [];

        foreach ($categories as $category) {
            if ($category->slug == "predication") { // Modif si on genere le json des predications
                $postStatus = "publish";
                $orderType  = "DESC";
            } else if ($category->slug == "etude-biblique") {
                continue; // On prend uniquement les sous-categories
            }

            $tmp[] = $category->slug;
        }

        $categories = $tmp;
        $criteria = [
            "post_type" => "post",
            "tax_query" => [
                [
                    "taxonomy" => "category",
                    "field"    => "slug",
                    "terms"    => $categories
                ]
            ],
            "orderby"        => "post_date",
            "order"          => $orderType,
            "post_status"    => $postStatus,
            'posts_per_page' => '-1'
        ];

        // Lancement de la recherche
        $the_query = new WP_Query($criteria);
        $data   = [];
        $width  = 78;
        $height = 52;
        $paddingTop = 14;
        $paddingLeft = 10;

        // Parcours des articles trouves
        foreach ($the_query->posts as $post) {
            $content = apply_filters('the_content', $post->post_content);
            $biblicalRef = extract_text_from_tag("class", "ref-biblique", $content);

            $media = "";
            $image = "";
            $title = get_the_title($post->ID);
            $isPublished = get_post_status($post->ID) == 'publish';
            if ( ! $isMobile ) {
                $image = get_the_post_thumbnail_url( $post->ID, [ $width, $height ] );
                /*
                if (endsWith($image, "&ssl=1")) {
                    $image = substr($image, 0, strlen($image) - 6);
                } */

                // On met l'image par defaut si non definie
                // Concerne les EB annoncées, mais non publiées
                if ( $image == null || $image === false ) {
                    $image = $isPublished ? "https://espritdeliberte.leswoody.net/wp-content/uploads/2017/08/v6.png" :
                            "https://espritdeliberte.leswoody.net/wp-content/uploads/2017/08/vide.png";
                }

                // On place sur la gauche du titre l'image de l'article
                $tooltip = $title;
                $title =
                    "<div style='overflow: hidden; max-height: " . $height . "px; float: left' >" .
                        "<img src='" . $image . "' width='" . $width . "px' />" .
                    "</div>" .
                    "<div style='word-wrap: break-word; padding-top: " . $paddingTop . "px; padding-left: " . $paddingLeft . "px; display: inline-flex' >" .
                        $title .
                    "</div>";
            }

            // Affiche un lien si l'article est publie
            if ( $isPublished ) {
                $title = "<a href='" . get_post_permalink($post->ID) . "' target='_blank' title='" . $tooltip . "'>" . $title . "</a>";
                $media = findMedia($post);
            }

            $formatDate  = $isMobile ? 'd/m' : 'd/m/Y';
            $displayDate = get_the_time($formatDate, $post->ID);
            $timestamp   = get_the_time('G'        , $post->ID);

            $data[] = [
                "date" => [
                    "display"   => surroundWithDiv($displayDate, true),
                    "timestamp" => $timestamp
                ],
                "title"  => $title,
                "link"   => surroundWithDiv($media, false),
                "refbib" => surroundWithDiv($biblicalRef, false)
            ];
        }

        $recordsTotal = $the_query->found_posts;

        // Restore original Post Data
        wp_reset_postdata();

        $response = [
            "draw" => intval($recordsTotal),
            "recordsTotal" => $recordsTotal,
            "data" => $data
        ];

        $json = json_encode($response);
        $fname = dirname(__FILE__) . "/json/" . join('-', $categories);
        if ($isMobile) {
            $fname .= "_mobile";
        }

        $fname .= ".json";

        $fp = fopen($fname, 'w');
        fwrite($fp, $json);
        fclose($fp);
    } catch (Exception $e) {
        var_dump('Exception reçue : ' . $e->getMessage());
    }
}

function wpdf_redirect_par_wp() {
    global $wp;

    if (! is_404()) {
        return;
    }

    // Pour ne pas casser les anciens liens
    $wpdf_liste_redirections = [
        '/liste-des-predications' => 'page-des-cultes'
    ];

    $wpdf_explode_request = explode("/", $wp->request); // rechercher les redirections génériques
    $wpdf_nouvelle_adresse = ''; // variable qui va contenir la nouvelle adresse
    $wpdf_modif_adresse = false; // indique si une redirection a été trouvée dans le tableau

    foreach ($wpdf_explode_request as $wpdf_elt_tableau) {
        if (array_key_exists($wpdf_nouvelle_adresse . '/' . $wpdf_elt_tableau . '*', $wpdf_liste_redirections)) {
            $wpdf_modif_adresse = true; // une redirection a été trouvée
            $wpdf_nouvelle_adresse = '/' . $wpdf_liste_redirections[$wpdf_nouvelle_adresse . '/' . $wpdf_elt_tableau . '*']; // l'élément doit être remplacé dans la nouvelle adresse
        } else {
            // si pas trouvé dans le tableau, l'élément doit être conservé dans la nouvelle adresse
            $wpdf_nouvelle_adresse .= '/' . $wpdf_elt_tableau;
        }
    }

    if (array_key_exists($wpdf_nouvelle_adresse, $wpdf_liste_redirections)) {
        $wpdf_modif_adresse = true; // une redirection a été trouvée
        $wpdf_nouvelle_adresse = '/' . $wpdf_liste_redirections[$wpdf_nouvelle_adresse];
    }

    $wpdf_domaine_site_cible = site_url(); // adresse domaine cible
    $query_string = filter_input(INPUT_SERVER, 'QUERY_STRING');
    $wpdf_param_url = ( strlen($query_string) ) ? '?' . $query_string : ''; //	récupérer le paramétrage de l'url : "?clé = valeur"
    $nouvelle_adresse = $wpdf_modif_adresse ? $wpdf_domaine_site_cible . $wpdf_nouvelle_adresse . '/' . $wpdf_param_url : $wpdf_domaine_site_cible;
    wp_redirect(esc_url($nouvelle_adresse), 301);
    exit; // toujours ajouter "exit" après une redirection
}

add_action('template_redirect', 'wpdf_redirect_par_wp');


// -----------------------------------------------------------------------------
//                   FONCTIONS D'ADMIN
// -----------------------------------------------------------------------------


/** Step 1. */
function my_esprit_de_liberte_menu() {
    add_options_page( 'Esprit de Liberté - Outils', 'Outils Esprit libre', 'manage_options', 'outils-esprit-libre', 'plugin_esprit_de_liberte_outils' );
}

/** Step 2 (from text above). */
add_action( 'admin_menu', 'my_esprit_de_liberte_menu' );

/** Step 3. */
function plugin_esprit_de_liberte_outils() {
    if (! current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    echo "<iframe src='" . get_stylesheet_directory_uri() . "/admin/index.php" . "' width='1000' heigth='500' />";
}


/*
function my_admin_menu() {
    add_menu_page (
            'Titre de page',
            'Esprit de liberté',
            'manage_options',
            'myplugin/myplugin-admin-page.php',
            '', //'myplguin_admin_page',
            'http://espritdeliberte.leswoody.net/wp-content/uploads/2017/08/w6.svg',
            2 ); // Juste en dessous de Tableau de bord

    add_submenu_page( 'esprit-de-liberte/esprit-de-liberte.php', 'My Sub Level Menu Example', 'Ajouter un culte', 'manage_options', 'myplugin/myplugin-admin-sub-page.php', 'myplguin_admin_sub_page' );
    add_submenu_page( 'myplugin/myplugin-admin-page.php', 'My Sub Level Menu Example', 'Générer les tableaux', 'manage_options', 'myplugin/myplugin-admin-sub-page.php', 'myplguin_admin_sub_page' );
}
add_action( 'admin_menu', 'my_admin_menu' );
*/

/*
function myplguin_admin_page(){
	?>
	<div class="wrap">
		<h2>Welcome To My Plugin</h2>
	</div>
	<?php
}

add_action( 'admin_menu', 'my_admin_menu' );
*/

/*
  $COMMON_PATH = get_stylesheet_directory_uri() . "/common";

  require_once $COMMON_PATH . "/createPost.php";


  function create_post( $att_id ) {

  $link = wp_get_attachment_link( $att_id );
  $filename = get_attached_file( $att_id );
  $ext = pathinfo($filename, PATHINFO_EXTENSION);

  if ( $ext != "mp3" )
  return;

  //$tag = id3_get_tag( $filename, ID3_V2_3 ); //
  $tag = wp_get_attachment_metadata( $att_id );
  print_r ($tag);

  foreach ($tag as $t)
  echo " val: " . $t;
  }

  add_action('add_attachment','create_post', 1, 1);
 */
