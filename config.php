<?php

ini_set( "display_errors", true ); //TODO: Disable `display_errors` on production server.
date_default_timezone_set( "Europe/Amsterdam" );

/**
 * FQDN (Domain / IP) for the Database.
 *
 */
const DB_FQDN = "127.0.0.1";
/**
 * Database name.
 *
 */
const DB_DATABASE = "methulator";
/**
 * Username for the database.
 *
 */
const DB_USERNAME = "root";
/**
 * Password for the database.
 *
 * @internal
 */
const DB_PASSWORD = "";

/**
 * Key for "encrypting" the JWT token.
 * @internal
 */
const JWT_KEY = "1234"; // TODO: MAKE A BETTER TOKEN

/**
 * Directory that contains all class files.
 */
const CLASS_PATH = "classes";
/**
 * Directory / Route that contains all api endpoints.
 */
const API_PATH = "api";

require( __DIR__ . '/vendor/autoload.php' );

require( "firebase/jwt/BeforeValidException.php" );
require( "firebase/jwt/CachedKeySet.php" );
require( "firebase/jwt/ExpiredException.php" );
require( "firebase/jwt/SignatureInvalidException.php" );
require( "firebase/jwt/Key.php" );
require( "firebase/jwt/JWK.php" );
require( "firebase/jwt/JWT.php" );

require( CLASS_PATH . "/Enum.php" );
require( CLASS_PATH . "/ErrorCodes.php" );
require( CLASS_PATH . "/PermissionFlag.php" );
require( CLASS_PATH . "/MethTransaction.php" );
require( CLASS_PATH . "/Player.php" );
require( CLASS_PATH . "/Account.php" );

/**
 * Handles any exceptions
 *
 * @param $exception
 *
 * @return void
 */
function handleException($exception ) {
    echo "Sorry, a problem occurred. Please try later.";
    error_log( $exception->getMessage() );
}

set_exception_handler( 'handleException' );