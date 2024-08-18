<?php
    $parse_uri = explode( 'wp-content', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME') );
    $path = substr($parse_uri[0], 0, strpos($parse_uri[0], 'index.php' ));
    require_once( $path . 'wp-load.php' );

    $theme = 'overcast';
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/v/ju/jq-3.7.0/dt-2.1.4/b-3.1.1/fh-4.0.1/r-3.0.2/datatables.min.css" rel="stylesheet">

<script src="https://cdn.datatables.net/v/ju/jq-3.7.0/dt-2.1.4/b-3.1.1/fh-4.0.1/r-3.0.2/datatables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>


<!--

<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.14.0/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.0/themes/<?= $theme ?>/jquery-ui.min.css" />



<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.4/css/dataTables.jqueryui.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/2.1.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/2.1.4/js/dataTables.jqueryui.min.js"></script>


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.1.1/css/buttons.jqueryui.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.jqueryui.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/4.0.1/css/fixedHeader.dataTables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/fixedheader/4.0.1/js/dataTables.fixedHeader.min.js"/>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
-->


<script type="text/javascript" src="<?= get_stylesheet_directory_uri() . '/common/scripts/' ?>accent-neutralise.js"></script>
<script type="text/javascript" src="<?= get_stylesheet_directory_uri() . '/common/scripts/' ?>mousetrap.min.js"></script>
