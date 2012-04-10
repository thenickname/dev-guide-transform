<?php

require_once 'Main.php';

set_error_handler( function ( $errorLevel, $errorDescription, $errorOriginFile, $errorOriginLine ) {
  die( "\nError: '$errorDescription' in file '$errorOriginFile' in line $errorOriginLine\n\n" );
} );

Main::run( $argv[ 1 ], $argv[ 2 ] );

?>