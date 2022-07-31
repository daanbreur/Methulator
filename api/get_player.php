<?php
    require_once "../config.php";

    session_start();
    if (!isset($_SESSION['user'])) $_SESSION['user'] = new Account();
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        if (preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            $jwt = $matches[1];
            if ($jwt) {
                $_SESSION['user']->authenticateJwt($jwt);
            }
        }
    }
    if (!$_SESSION['user']->authenticated) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(array("success" => false, "data" => [], "message" => "Not authenticated"));
        exit;
    }

    if (isset($_GET['playerid'])) {
        if (!$_SESSION['user']->canViewPlayer()) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(array("success" => false, "data" => [], "message" => "You do not have the permissions to do this"));
            exit;
        }

        $data = new Player($_GET['playerid']);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(array("success" => true, "data" => $data));

        exit;
    } else {
        if (!$_SESSION['user']->canViewAllPlayers()) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(array("success" => false, "data" => [], "message" => "You do not have the permissions to do this"));
            exit;
        }

        $connection = mysqli_connect(DB_FQDN, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if ( mysqli_connect_errno() ) {
            exit('Failed to connect to MySQL: ' . mysqli_connect_error());
        }

        if ($stmt = $connection->prepare('SELECT players.id FROM players')) {
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->free_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                $data = array();
                while ($row = $result->fetch_assoc()) {
                    $player = new Player($row['id']);
                    $data[] = $player;
                }
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode(array("success" => true, "data" => $data));
            } else {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode(array("success" => true, "data" => []));
            }
            exit;
        }
    }