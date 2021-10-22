<?php
    /*function getDBConnection(){
        $connectionParams = [
            'host' => DB_HOST,
            'dbname'=> DB_NAME,
            'user'=> DB_USER,
            'password'=>DB_PASS,
            'driver'=>'pdo_mysql',
            'charset'=>'utf8mb4'
        ];

        $connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);

        try {
            $connection -> connect();
            return $connection;
        }catch (\Doctrine\DBAL\Exception\ConnectionException $e){
            echo 'Could not connect to database';
        }
    }*/

    class DatabaseConnector {
        const DB_HOST = 'mysqldb';
        const DB_USER = 'root';
        const DB_PASS = 'Azerty123';
        const DB_NAME = 'brief_op_zak';

        static function getConnection (): \Doctrine\DBAL\Connection {
            $connectionParams = [
                'url' => 'mysql://' . self::DB_USER . ':' . self::DB_PASS . '@' . self::DB_HOST . '/' . self::DB_NAME
            ];

            $connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);

            try {
                $connection->connect();
            }
            catch (\Doctrine\DBAL\Exception\ConnectionException $e) {
                echo($e->getMessage());
                exit();
            }
            return $connection;
        }
    }
