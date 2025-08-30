<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 29.08.2025
 * Time: 17:34
 */

require_once __DIR__ . '/DB.php';


class TaskController
{

    private function sendResponse( array $data, int $httpCode = 200 ) : void
    {
        echo json_encode( [
            'status'   => http_response_code( $httpCode ),
            'response' => $data
        ], JSON_UNESCAPED_UNICODE );

        exit;
    }

    private function sendError( string $error, int $httpCode = 400, $apiCode = 0 ) : void
    {
        echo json_encode( [
            'status' => http_response_code( $httpCode ),
            'error'  => [
                'error_code' => $apiCode,
                'error_msg'  => $error
            ]
        ], JSON_UNESCAPED_UNICODE );

        exit;
    }

    private function validate( array $data, bool $isUpdate = false ) : string
    {
        if ( !$isUpdate || isset( $data['title'] ) )
        {
            if ( !isset( $data['title'] ) || trim( $data['title'] ) === '' )
            {
                return 'Поле «title» обязательно и не может быть пустым';
            }
            else if ( mb_strlen( $data['title'] ) > 191 )
            {
                return 'Поле «title» должно содержать не более 191 символа';
            }
        }

        if ( !$isUpdate || isset( $data['description'] ) )
        {
            if ( !isset( $data['description'] ) || trim( $data['description'] ) === '' )
            {
                return 'Поле «description» обязательно и не может быть пустым';
            }
        }

        if ( !$isUpdate || isset( $data['status'] ) )
        {
            if ( !isset( $data['status'] ) || trim( $data['status'] ) === '' )
            {
                return 'Поле «status» обязательно';
            }
            else if ( !in_array( $data['status'], [ 'pending', 'in_progress', 'done' ] ) )
            {
                return 'Поле «status» может принимать только значения: pending, in_progress, done';
            }
        }

        return '';
    }

    private function timestamp() : string
    {
        return date( 'Y-m-d H:i:s', time() );
    }

    public function index() : void
    {
        $this->sendResponse( DB::i()->query( "SELECT * FROM `tasks` ORDER BY `id` DESC" )->fetchAll() );
    }

    public function show( int $id ) : void
    {
        $stmt = DB::i()->prepare( "SELECT * FROM `tasks` WHERE `id` = ?" );
        $stmt->execute( [ $id ] );

        if ( !( $task = $stmt->fetch() ) )
        {
            $this->sendError( "Задачи по ID #$id не найдено", 404, 1 );
        }

        $this->sendResponse( $task );
    }

    public function store() : void
    {
        $data = json_decode( file_get_contents( 'php://input' ), true );

        if ( $error = $this->validate( $data ) )
        {
            $this->sendError( $error, 422, 2 );
        }

        $time = $this->timestamp();

        $stmt = DB::i()->prepare( "INSERT INTO `tasks` (`title`, `description`, `status`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?)" );
        $stmt->execute( [ $data['title'], $data['description'], $data['status'], $time, $time ] );

        $this->sendResponse( [ 'task_id' => DB::i()->lastInsertId() ], 201 );
    }

    public function update( int $id ) : void
    {
        $data = json_decode( file_get_contents( "php://input" ), true );

        if ( $error = $this->validate( $data, true ) )
        {
            $this->sendError( $error, 422, 2 );
        }

        $fields = [];
        $values = [];

        foreach ( [ 'title', 'description', 'status' ] as $field )
        {
            if ( isset( $data[$field] ) )
            {
                $fields[] = "`$field` = ?";
                $values[] = $data[$field];
            }
        }

        if ( empty( $fields ) )
        {
            $this->sendError( 'Нет полей для обновления', 422, 3 );
        }

        array_push( $values, $this->timestamp(), $id );

        $sql = "UPDATE `tasks` SET " . implode( ', ', $fields ) . ", `updated_at` = ? WHERE `id` = ?";
        $stmt = DB::i()->prepare( $sql );
        $stmt->execute( $values );

        $this->sendResponse( [ 'updated' => true ] );
    }

    public function destroy( int $id ) : void
    {
        $stmt = DB::i()->prepare( "DELETE FROM `tasks` WHERE `id` = ?" );
        $stmt->execute( [ $id ] );

        $this->sendResponse( [ 'deleted' => true ] );
    }

}
