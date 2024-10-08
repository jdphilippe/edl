<?php
/*
 * Template Name: Page des cultes
 */

//ini_set('display_errors',1);
//error_reporting(E_ALL);

get_header();

$THEME_PATH  = get_stylesheet_directory_uri();
$COMMON_PATH = $THEME_PATH . "/common";

function isMobile() {
    //return 1;
    return wp_is_mobile();
}


$inc_common_ui = __DIR__ . "/../common/common" . ( isMobile() ? "_mobile" : "" ) . ".php"; // pour inclure common.php ou common_mobile.php selon le cas
require_once $inc_common_ui;

if (isMobile()) {
    ?>
    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#aide" aria-expanded="false" aria-controls="collapseExample">
        Aide
    </button>
<?php }
?>

<div id="aide" class="collapse dialog" title="Aide - Page des cultes" >
    <div class="panel-body">
        <ul>
            <li>Vous pouvez trier sur la colonne de votre choix en cliquant sur son entête.
            <li>Vous pouvez faire une recherche en utilisant le champ prévu à cet effet. Elle s'effectue sur toute la grille et au fur et à mesure de la saisie, les éléments recherchés apparaîssent.
            <li>Le raccourci clavier CTRL F (ou Command F sur Mac) active le champ de la recherche.
            <li>Pour accéder au texte de la prédication, vous pouvez cliquer sur le lien présent dans la colonne "Titre".
            <li>Pour regarder une prédication, vous pouvez cliquer sur l'icone <img src='<?= $COMMON_PATH ?>/images/tv.png' style='vertical-align:middle;' alt='Regarder la prédication' title='Regarder la prédication'/>
            <li>Pour écouter le culte en entier, vous pouvez cliquer sur l'icone <img src='<?= $COMMON_PATH ?>/images/button_play.png' style='vertical-align:middle;' alt='Ecouter le culte' title='Ecouter le culte'/>
            <li>Pour écouter une prédication, vous pouvez cliquer sur l'icone <img src='<?= $COMMON_PATH ?>/images/button_play_predic.png' style='vertical-align:middle;' alt='Ecouter la prédication' title='Ecouter la prédication'/>
            <li>Pour télécharger un culte ou une prédication, vous pouvez faire un clic droit sur ces mêmes icônes, et selectionner "Enregistrer la cible du lien sous..."
            <li>Si vous êtes sur téléphone ou un écran trop petit pour tout afficher, l'icône <img src='<?= $COMMON_PATH ?>/images/ic_plus.png' style='vertical-align:middle;' alt=""/> apparaît à gauche de la date. Vous pouvez alors cliquer dessus pour accéder aux colonnes cachées.
        </ul>
    </div>
</div>

<script type="text/javascript">

    ;
    (function (window, $, undefined) {

        function isMobile()
        {
            return ("<?= isMobile() ?>" === "1");
        }

        function isLoggedIn()
        {
            return ("<?= isLoggedIn() ?>" === "1");
        }

        function isScrolledIntoView(elem)
        {
            var docViewTop = $(window).scrollTop();
            var docViewBottom = docViewTop + $(window).height();

            var elemTop = $(elem).offset().top;
            var elemBottom = elemTop + $(elem).height();

            return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
        }

        $(document).ready(function () {

            var category = "predication",
                fname = category,
                oldHeaderFixedValue = 0,
                sortable = ! isMobile(),
                dateColWidth = isMobile() ? 80 : 100,
                linkColWidth = isLoggedIn() ? 150 : 100;
                //linkColWidth = isLoggedIn() ? 89 : 54;

            if (isMobile())
                fname += "_mobile";

            if (! isLoggedIn())
                fname += "_guest";

            fname += ".json";

            var table = $('#predications').DataTable({
                bJQueryUI: ! isMobile(),
                //dom: 'Bfrtip', // Place une barre d'outil pour mettre le bouton Aide dedans
                dom: 'frtip',
                fixedHeader: true,
                paging: false,
                sort: sortable,
                responsive: true,
                language: {
                    "sProcessing": "Traitement en cours...",
                    "sSearch": "Recherche&nbsp;:",
                    "sLengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
                    "sInfo":           "_TOTAL_ pr&eacute;dications disponibles",
                    "sInfoEmpty":      "Aucune pr&eacute;dication ne correpond aux crit&egrave;res de recherche",
                    "sInfoFiltered":   "(filtr&eacute;, sur un total de _MAX_)",
                    "sInfoPostFix":    "",
                    "sLoadingRecords": "Chargement en cours...",
                    "sZeroRecords":    "Aucune pr&eacute;dication &agrave; afficher",
                    "sEmptyTable": "Aucune pr&eacute;dication disponible",
                    "oPaginate": {
                        "sFirst": "Premier",
                        "sPrevious": "Pr&eacute;c&eacute;dent",
                        "sNext": "Suivant",
                        "sLast": "Dernier"
                    },
                    "oAria": {
                        "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                        "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
                    }
                },
                ajax: {
                    url: "<?= $THEME_PATH ?>/json/" + fname
                },
                columns: [
                    {
                        data: {
                            _:    "date.display",
                            sort: "date.timestamp"
                        }
                    },
                    {data: "title"},
                    {data: "link", sortable: false, searchable: false},
                    {data: "refbib"}
                ],
                columnDefs: [
                    {width: dateColWidth, targets: 0}, // Date
                    {width: linkColWidth, targets: 2}, // Liens
                    {width: 170,          targets: 3}  // Texte biblique
                ],
                order: [[0, "desc"]],
                fnInitComplete: function () {

                    // Enleve les accents du champ de recherche
                    var fnSearch = function () {
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
                },
                buttons: ['help']
            });

            var helpWindowClosed = true; // Par defaut, la boite d'aide est fermee
            function onHelpWindowClose()
            {
                var action = helpWindowClosed ? 'open' : 'close';
                helpWindowClosed = !helpWindowClosed;
                var btnTitle = helpWindowClosed ? 'Aide' : 'Fermer';

                $("#aide").dialog(action);
                $(".buttons-alert > span").html(btnTitle); // Modification du label du bouton
                if (helpWindowClosed)
                    $(".buttons-alert").removeClass("active");
                else
                    $(".buttons-alert").addClass("active");
            }

            // Definition du bouton d'aide
            $.fn.dataTable.ext.buttons.help = {
                className: 'buttons-alert',
                text: 'Aide',
                action: onHelpWindowClose
            };

            if (isMobile()) {
            } else {
                /*
                $(window).scroll(function() {
                    var headerOffset = isScrolledIntoView(".header-main" ) ? $( ".header-main" ).height() : 0;

                    if (headerOffset === oldHeaderFixedValue)
                        return;

                    headerOffset += $("#wpadminbar").height();
                    table.fixedHeader.headerOffset(headerOffset);
                    table.fixedHeader.adjust();
                    oldHeaderFixedValue = headerOffset;
                });*/

                $("#aide").dialog({
                    autoOpen: false,
                    position: {
                        my: "left top",
                        at: "left bottom",
                        of: ".buttons-alert"
                    },
                    width: 800,
                    show: {
                        effect: "blind",
                        duration: 1000
                    },
                    hide: {
                        effect: "blind",
                        duration: 600
                    },
                    closeOnEscape: true,
                    closeText: "Fermer",
                    close: function () {
                        if (! helpWindowClosed)
                            onHelpWindowClose();
                    }
                });
            }
        });
    })(window, jQuery);
</script>

<table class="dataTable display responsive table table-striped table-hover" id="predications" style="width: 100%;">
    <thead>
        <tr>
            <th style='text-align: center' class="all">Date</th>
            <th style='text-align: center' class="all">Titre</th>
            <th style='text-align: center' class="min-tablet-l">Liens</th>
            <th style='text-align: center' class="min-tablet-p">Textes bibliques</th>
        </tr>
    </thead>
</table>

<?php
get_footer();
