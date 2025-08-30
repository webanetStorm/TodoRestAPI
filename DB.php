<?php

/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 29.08.2025
 * Time: 17:26
 */
class DB
{

    private const string HOST = 'MySQL-5.6';

    private const string DB = 'todo';

    private const string USER = 'root';

    private const string PASS = '';


    private static ?PDO $_instance = null;


    public static function i() : PDO
    {
        if ( self::$_instance instanceof PDO )
        {
            return self::$_instance;
        }

        try
        {
            return self::$_instance = new PDO( 'mysql:host=' . self::HOST . ';dbname=' . self::DB, self::USER, self::PASS, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ] );
        }
        catch ( PDOException $e )
        {
            exit( $e->getMessage() );
        }
    }

}
