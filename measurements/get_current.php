<?php
    /*!
        Get current measurement value.
    */

    /*
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    */

    include '../../settings.cfg';  // Settings

    header("Content-Type: application/json; charset=UTF-8");
    
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
        "WHERE  guid='" . $guid . "' ORDER BY idx DESC LIMIT 1;" );

    $outp = array();
    $outp = $result->fetch_all( MYSQLI_ASSOC );

    // free memory associated with result
    $result->close();

    // close connection
    $conn->close();

    echo json_encode( $outp );

?>