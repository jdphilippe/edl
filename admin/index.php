<?php
    $parse_uri = explode( 'wp-content', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME') );
    require_once( $parse_uri[0] . 'wp-load.php' );

    $urlCreateJSONFiles = get_stylesheet_directory_uri() . '/admin/createJSONFiles.php';
    $jsonDir = get_stylesheet_directory_uri() . '/json';

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Admin Esprit de Liberté</title>
        <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
        <script type="text/javascript">
            function onGenerateJSONFilesClick() {
                $("#result").html("");
                $.get("<?= $urlCreateJSONFiles ?>", function() {
                    $("#result").html("Fichiers générés");
                }).fail(function(res) {
                    let msg = "Attention: erreur '" + res.statusText + "'<br>";
                    msg += "C'est l'heure de contacter votre admin préféré ou de regarder ce qui se passe dans le répertoire des JSON.";

                    $("#result").html(msg);
                });
            }
        </script>
    </head>
    <body>
        <div id="generate-json-files">
            Répertoire des fichiers JSON: <a href='<?= $jsonDir ?>' target='blank'><?= $jsonDir ?></a><br>
            <button type="button" onclick="onGenerateJSONFilesClick()">Regénérer les tableaux</button>
            <br>
            <span id="result"></span>
        </div>
    </body>
</html>