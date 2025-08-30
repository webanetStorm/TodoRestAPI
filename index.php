<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 29.08.2025
 * Time: 17:21
 */


header( 'Content-Type: application/json' );

require_once __DIR__ . '/TaskController.php';


$uri = explode( '/', trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' ) );
$method = $_SERVER['REQUEST_METHOD'];

$controller = new TaskController();

if ( $uri[0] == 'tasks' )
{
    $id = $uri[1] ?? null;

    switch ( $method )
    {
        case 'GET':
        {
            $id ? $controller->show( $id ) : $controller->index();

            break;
        }
        case 'POST':
        {
            $controller->store();

            break;
        }
        case 'PUT':
        {
            if ( $id )
            {
                $controller->update( $id );
            }

            break;
        }
        case 'DELETE':
        {
            if ( $id )
            {
                $controller->destroy( $id );
            }

            break;
        }
    }
}
