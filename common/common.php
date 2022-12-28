<?php
    $parse_uri = explode( 'wp-content', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME') );
    $path = substr($parse_uri[0], 0, strpos($parse_uri[0], 'index.php' ));
    require_once( $path . 'wp-load.php' );

    $theme = 'overcast';
?>

<!--

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.15/fh-3.1.2/r-2.1.1/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/<?= $theme ?>/jquery-ui.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css" />

<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.15/fh-3.1.2/r-2.1.1/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>

-->


<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/<?= $theme ?>/jquery-ui.min.css" />


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.jqueryui.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.jqueryui.min.js"></script>


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.jqueryui.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.jqueryui.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.2.2/css/fixedHeader.dataTables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.2.2/js/dataTables.fixedHeader.min.js"/>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>



<!--
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/ju/jq-3.6.0/dt-1.11.3/b-2.0.1/fh-3.2.0/r-2.2.9/datatables.min.css"/>

<script type="text/javascript" src="https://cdn.datatables.net/v/ju/jq-3.6.0/dt-1.11.3/b-2.0.1/fh-3.2.0/r-2.2.9/datatables.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

-->

<script type="text/javascript" src="<?= get_stylesheet_directory_uri() . '/common/scripts/' ?>accent-neutralise.js"></script>
<script type="text/javascript" src="<?= get_stylesheet_directory_uri() . '/common/scripts/' ?>mousetrap.min.js"></script>
