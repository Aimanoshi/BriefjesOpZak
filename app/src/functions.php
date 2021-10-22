<?php
    $basePath = __DIR__ . '/../';
    require_once $basePath . 'config/database.php';

    //DB connection
    function getDBConnection (): \Doctrine\DBAL\Connection {
        $connectionParams = [
            'host' => DB_HOST,
            'dbname' => DB_NAME,
            'user' => DB_USER,
            'password' => DB_PASS,
            'driver' => 'pdo_mysql',
            'charset' => 'utf8mb4'
        ];

        $connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);

        try {
            $connection->connect();
            return $connection;
        }
        catch (\Doctrine\DBAL\Exception\ConnectionException $e) {
            echo 'could not connect to db';
            echo $e;
            exit;
        }
    }

    function getAllOrganisations (): array {
        $connection = getDBConnection();
        $stmt = $connection->prepare('SELECT * FROM `organisations`');
        $stmt->execute([]);
        $Organisations = $stmt->fetchAllAssociative();
        return $Organisations;
    }

    function getOneOrganisation (string $id): array {
        $connection = getDBConnection();
        $stmt = $connection->prepare('SELECT * FROM `organisations` WHERE Id =?');
        $stmt->execute(array($id));
        $organisation = $stmt->fetchAssociative();
        return $organisation;
    }

    function getChannels (string $id): array {
        $connection = getDBConnection();
        $stmt = $connection->prepare('SELECT channels.Id, channels.name, organisations.name AS organisation_name FROM `channels` LEFT JOIN organisations ON organisations_Id = organisations.Id WHERE organisations_Id =?');
        $stmt->execute(array($id));
        $channels = $stmt->fetchAllAssociative();
        return $channels;
    }

    function getOneChannel (string $id): array {
        $connection = getDBConnection();
        $stmt = $connection->prepare('SELECT channels.Id, channels.name, organisations.name AS organisation_name, channels.description FROM `channels` LEFT JOIN organisations ON organisations_Id = organisations.Id WHERE channels.Id =?');
        $stmt->execute(array($id));
        $channel = $stmt->fetchAssociative();
        return $channel;

    }

    function getSubscribedChannels (string $userId): array {
        $connection = getDBConnection();
        $stmt = $connection->prepare('SELECT channels_Id, channels.name, organisations.name AS organisation_name FROM `subscriptions` LEFT JOIN channels ON channels_Id = channels.Id LEFT JOIN organisations ON organisations.Id = organisations_Id WHERE subscriptions.users_Id = ? AND subscriptions.accepted = ?');
        $stmt->execute([$userId, 1]);
        $channels = $stmt->fetchAllAssociative();
        return $channels;
    }

    function getMessages (string $channelId): array {
        $connection = getDBConnection();
        $stmt = $connection->prepare('SELECT `title`, `description`, `added_date` FROM `messages` WHERE `channels_Id` = ?');
        $stmt->execute(array($channelId));
        $messages = $stmt->fetchAllAssociative();
        return $messages;
    }

/*    function alert ($msg) {
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }*/