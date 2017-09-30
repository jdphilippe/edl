<?php
/*
    header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
*/
    $parse_uri = explode( 'wp-content', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME') );
    $path = substr($parse_uri[0], 0, strpos($parse_uri[0], "index.php"));
    require_once( $path . 'wp-load.php' );
?>

<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.bootstrap.min.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css" />

<link rel="stylesheet" type="text/css" href="<?= get_stylesheet_directory_uri() ?>/style.css" />


<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

<script type="text/javascript" src="//cdn.datatables.net/v/bs/dt-1.10.15/fh-3.1.2/r-2.1.1/datatables.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>
