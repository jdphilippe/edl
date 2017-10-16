<?php
/*
 * Template Name: Page des etudes bibliques
 */

 ini_set('display_errors',1);
 error_reporting(E_ALL);
// var_dump( $the_query->posts );

get_header();

$THEME_PATH = get_stylesheet_directory_uri();

function isMobile() {
    //return 1;
    return wp_is_mobile();
}

$inc_common_ui = dirname(__FILE__) . "/../common/common" . ( isMobile() ? "_mobile" : "" ) . ".php"; // pour inclure common.php ou common_mobile.php selon le cas
require_once $inc_common_ui;

$activeTab = -1; // Onglet actif

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
        $categories  = get_categories( array (
            'hide_empty' => 0,
            'child_of'   => $category_id->term_id,
            'orderby'    => 'slug',
            'order'      => 'ASC'
        ));

        /*
         * Recherche de l'onglet a selectionner par defaut
         * La recherche est basee sur la date indiquee dans les identifiants des sous-categories etude-biblique.
         * Format: YYYYMM
         * On active l'onglet dont la date passee est la plus proche de la date courante
         */

        $currentYearMonth = intval(date("Ym", time()));
        $numTab = 0;
        $categoryList = [];
        foreach ($categories as $category) {
            $categoryList[] = $category->slug;
            $tabYearMonth = intval(substr($category->slug, 0, 6)); // Les identifiants des categories commencent par YYYYMM
            if ($currentYearMonth >= $tabYearMonth) {
                $activeTab = $numTab; // On active l'onglet des etudes bibliques de l'annee en cours
            }

            // Ajout d'un onglet
            echo "<li id='li_" . $category->slug . "' role='presentation'><a href='#tab_' onclick='javascript:selectTab(\"" . $category->slug . "\"); return false;' id='a_" . $category->slug . "'>" . $category->name . "</a></li>\n"; // Le "return false" permet de ne pas executer le lien "href". Ce lien est necessaire pour les onglets, mais pose probleme pour le mode mobile
            $numTab++;
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
                    <th style='text-align: center' class='min-tablet-p'>Texte à l'étude</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">

    ;
    (function (window, $, undefined) {

        function isMobile() {
            //return "1";
            return ("<?= isMobile() ?>" === "1");
        }

        function isScrolledIntoView(elem) {
            var docViewTop = $(window).scrollTop();
            var docViewBottom = docViewTop + $(window).height();

            var elemTop = $(elem).offset().top;
            var elemBottom = elemTop + $(elem).height();

            return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
        }

        var table;
        $(document).ready(function () {
            var oldHeaderFixedValue = 0;

            if ($("#tabEB").tabs) {
                $("#tabEB").tabs({
                    active: getActiveTab()
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
                    {width: 80 , targets: 0}, // Date
                    {width: 65 , targets: 2}, // Liens 52
                    {width: 170, targets: 3}  // Texte a l'etude
                ],
                initComplete: function () {
                    /* On charge la grille uniquement quand le composant Datatable a fini de s'initialiser.
                     Sinon, genere une erreur https://datatables.net/manual/tech-notes/4
                     */
                    setActiveTab(getActiveTab());
                },
                drawCallback: function () {
                    // Une fois les lignes inserees - la hauteur etant occupee, on peut replacer le pied de page
                    // footerResize();
                }
            });

            if (! isMobile()) {
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

        // Pour caler le pied de page en bas lors d'un changement d'onglet
/*
        function footerResize() {
            var footerPosition = ($("body").height() + $("#footer").innerHeight() > $(window).height()) ? "inherit" : "fixed";
            $('#footer').css('position', footerPosition);
        }

        $("body").resize(function() {
            setTimeout(footerResize, 500);
        });
*/
        function getActiveTab() {
            return <?= $activeTab ?>;
        }

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

            if (isMobile())
                fname += "_mobile";

            fname += ".json";

            $.post("<?= $THEME_PATH ?>/json/" + fname, param, function (data) {
                // Redessine le tableau avec les nouvelles valeurs
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
                    $("div[id='tab_'].ui-widget-content a").css("color", "#22aadd"); // On remet les liens des titres en bleu (tabs surcharge la css)
                }
            }).error(function() {
                // En cas d'erreur 404. Selection d'un onglet sans EB publiee
                table.clear();
                table.draw();
            });
        };
    })(window, jQuery);
</script>

<?php
get_footer();
