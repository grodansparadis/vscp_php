<?php

    /*
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    */

    header("Content-Type: application/json; charset=UTF-8");
    
    // Get start date
    $dt = new DateTime();
    $from = $_GET["from"];
    trim($from);
    if ( 0 == strlen($from) ) {
        $from = $dt->format('Y-m-d\T00:00:00');
    }

    // Get end date
    $to = $_GET["to"];
    trim($to);
    if ( 0 == strlen($to) ) {
        $to = $dt->format('Y-m-d\T23:59:59');    
    }

    // Get sensor GUID
    $guid = $_GET["guid"];
    trim($guid);
    if ( 0 == strlen($guid) ) {
        $guid = 'FF:FF:FF:FF:FF:FF:FF:FF:61:00:08:01:92:AF:A8:10';    
    }    

    $conn = new mysqli("mysql690.loopia.se", "test@v188090", "secret82050", "vscp_org");

    if ( !$conn ){
	    die("Connection to database failed: " . $conn->error);
    }

    $result = $conn->query("SELECT date, value FROM `measurement` " .
        "WHERE ( date BETWEEN '" . $from . "' AND '" . $to . "' ) " .
        "AND guid='" . $guid . "';" );

    $outp = array();
    $outp = $result->fetch_all( MYSQLI_ASSOC );

    // free memory associated with result
    $result->close();

    // close connection
    $conn->close();

    echo json_encode( $outp );

?>