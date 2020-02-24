<?php
    $parse_uri = explode( 'wp-content', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME') );
    $path = substr($parse_uri[0], 0, strpos($parse_uri[0], 'index.php' ));
    require_once( $path . 'wp-load.php' );

    $theme = 'overcast';
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.15/fh-3.1.2/r-2.1.1/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/<?= $theme ?>/jquery-ui.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css" />

<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.15/fh-3.1.2/r-2.1.1/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>


<script type="text/javascript" src="<?= get_stylesheet_directory_uri() . '/common/scripts/' ?>accent-neutralise.js"></script>
<script type="text/javascript" src="<?= get_stylesheet_directory_uri() . '/common/scripts/' ?>mousetrap.min.js"></script>
