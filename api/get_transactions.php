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

    if (!$_SESSION['user']->canViewMethTransaction()) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(array("success" => false, "data" => [], "message" => "You do not have the permissions to do this"));
        exit;
    }

    $connection = mysqli_connect(DB_FQDN, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    if ( mysqli_connect_errno() ) {
        exit('Failed to connect to MySQL: ' . mysqli_connect_error());
    }

    if ($stmt = $connection->prepare('SELECT methTransactions.id FROM methTransactions')) {
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->free_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $transaction = new MethTransaction($row['id']);
                $transaction->transactionDate = $transaction->transactionDate->format("Y-m-d H:i:s");
                $data[] = $transaction;
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

//    TODO: Optimize by using x1,x2 inputs. Instead of always fetching the whole database.