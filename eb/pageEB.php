<?php
/*
 * Template Name: Page des etudes bibliques
 */

// ini_set('display_errors',1);
// error_reporting(E_ALL);
// var_dump( $the_query->posts );

get_header();

$THEME_PATH = get_stylesheet_directory_uri();

function isMobile() {
    return wp_is_mobile();
}

$inc_common_ui = __DIR__ . "/../common/common" . ( isMobile() ? "_mobile" : "" ) . ".php"; // pour inclure common.php ou common_mobile.php selon le cas
require_once $inc_common_ui;


// Recherche de la page demandee
$url = filter_input(INPUT_SERVER, 'REQUEST_URI');
$url = rtrim($url, '/'); // Enleve le dernier /
$tab = explode( '/', $url );
$cat_calling = end( $tab );
?>
<div id="tabEB">

    <!-- On place la barre d'onglet -->
    <ul class="nav nav-pills">
        <?php
        $category_id = get_category_by_slug( $cat_calling );
        if ($category_id === FALSE) {
            echo "L'URL de la page appelante n'est pas bonne: " . $cat_calling;
        }

        $categories  = get_categories( array (
            'hide_empty' => 0,
            'child_of'   => $category_id->term_id,
            'orderby'    => 'slug',
            'order'      => 'ASC'
        ));

        // Les plus recentes a gauche
        $categories = array_reverse( $categories );

        foreach ($categories as $category) {
            // Suppression du texte entre (), s'il y en a. Par ex: Notre pere (Catechisme) -> Notre Pere
            $pos = strpos($category->name, "(");
            if ($pos !== false) {
                $category->name = substr($category->name, 0, $pos -1);
            }

            // Ajout d'un onglet
            echo "<li id='li_" . $category->slug . "' role='presentation'><a href='#tab_' onclick='javascript:selectTab(\"" . $category->slug . "\"); return false;' id='a_" . $category->slug . "'>" . $category->name . "</a></li>\n"; // Le "return false" permet de ne pas executer le lien "href". Ce lien est necessaire pour les onglets, mais pose probleme pour le mode mobile
        }
        ?>
    </ul>

    <!-- On place le tableau des EB -->
    <div id='tab_'>
        <table class="dataTable display responsive table table-striped table-hover" id="table_rencontre" style="width: 100%;">
            <thead>
                <tr>
                    <th style='text-align: center' class='all'>Date</th>
                    <th style='text-align: center' class='all'>Titre de la séance</th>
                    <th style='text-align: center' class="min-tablet-p">Liens</th>
                    <th style='text-align: center' class='min-tablet-p'>Textes à l'étude</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">

    ;
    (function (window, $, undefined) {

        function isMobile() {
            return ("<?= isMobile() ?>" === "1");
        }

        function isScrolledIntoView(elem) {
            var docViewTop = $(window).scrollTop();
            var docViewBottom = docViewTop + $(window).height();

            var elemTop = $(elem).offset().top;
            var elemBottom = elemTop + $(elem).height();

            return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
        }

        var table,
            oldTab = "";

        $(document).ready(function () {
            var oldHeaderFixedValue = 0,
                dateColWidth = isMobile() ? 80 : 100;

            if ($("#tabEB").tabs) {
                $("#tabEB").tabs({
                    active: 0
                }).find('.ui-tabs-nav li').off('keydown'); // Pour bloquer la navigation au clavier
            }

            table = $('#table_rencontre').DataTable({
                destroy: true,
                fixedHeader: true,
                responsive: true,
                bJQueryUI: ! isMobile(),
                paging: false,
                searching: false,
                sort: false,
                info: false,
                language: {
                    emptyTable: "Ces rencontres ne sont pas encore disponibles"
                },
                "sDom": 'lfrtip',
                columns: [
                    {
                        data: {
                            _: "date.display"
                        }
                    },
                    {data: "title"},
                    {data: "link"},
                    {data: "refbib"}
                ],
                columnDefs: [
                    {width: dateColWidth, targets: 0}, // Date
                    {width: 90          , targets: 2}, // Liens
                    {width: 170         , targets: 3}  // Texte a l'etude
                ],
                initComplete: function () {
                    /* On charge la grille uniquement quand le composant Datatable a fini de s'initialiser.
                     Sinon, genere une erreur https://datatables.net/manual/tech-notes/4
                     */
                    setActiveTab(0);
                },
                drawCallback: function () {
                    // Une fois les lignes inserees - la hauteur etant occupee, on peut replacer le pied de page
                    //footerResize();
                }
            });

            if (! isMobile()) {
                /*
                $(window).scroll(function() {
                    var headerOffset = isScrolledIntoView(".header-main" ) ? $( ".header-main" ).height() : 0;

                    if (headerOffset === oldHeaderFixedValue)
                        return;

                    headerOffset += $("#wpadminbar").height();
                    table.fixedHeader.headerOffset(headerOffset);
                    table.fixedHeader.adjust();
                    oldHeaderFixedValue = headerOffset;
                });
                */
            }
        });

        // Pour caler le pied de page en bas lors d'un changement d'onglet
/*
        function footerResize() {
            var footerPosition = "inherited"; // ($("body").height() + $("#footer").innerHeight() > $(window).height()) ? "inherit" : "fixed";
            $('#footer').css('position', footerPosition);
        }

        $("body").resize(function() {
            setTimeout(footerResize, 500);
        });
*/
        function setActiveTab(numTab) {
            var tab = $("li[id^='li_']").eq(numTab);
            tab = tab.attr("id").replace("li_", "");
            $("#a_" + tab).trigger('click');
        }

        selectTab = function (tab) {
            var param = {
                category: tab,
                post_status: "any",
                order: "ASC",
                isMobile: isMobile()
            },
            fname = tab;

            if (tab == oldTab)
                return false; // Pour eviter de charger les memes donnees

            oldTab = tab;

            if (isMobile())
                fname += "_mobile";

            fname += ".json";

            $.post("<?= $THEME_PATH ?>/json/" + fname, param, function (data) {
                // Redessine le tableau avec les nouvelles valeurs

                if ( typeof data.data === 'undefined' )
                    data = JSON.parse( data ); // portage jQuery 3

                table.clear();
                table.rows.add(data.data);
                table.draw();

                if (isMobile()) {
                    // Si on est sur mobile, gestion du bouton actif.
                    $("li[id^='li_']").each(function (index, value) {
                        $(value).removeClass("active");
                        if ($(value).attr("id") === "li_" + tab)
                            $(value).addClass("active");
                    });
                } else {
                    // On remet les liens des titres en bleu (tabs surcharge la css)
                    $("li[role='tab'].ui-tabs-tab a")
                        .css("color", "#22aadd")
                        .css("background-color", "##f6f6f6");

                    $("li[role='tab'].ui-tabs-active a")
                        .css("color", "#FFFFFF")
                        .css("background-color", "#999999");

                    $("#table_rencontre a[href]")
                        .css("color", "#22aadd");
                }
            }).fail(function() {
                // En cas d'erreur 404. Selection d'un onglet sans EB publiee
                table.clear();
                table.draw();
            });
        };
    })(window, jQuery);
</script>

<?php
//get_footer();
