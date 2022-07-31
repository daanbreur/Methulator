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
        echo json_encode(array("success" => false, "message" => "Not authenticated"));
        exit;
    }

    if (!$_SESSION['user']->canCreateMethTransaction()) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(array("success" => false, "message" => "You do not have the permissions to do this"));
        exit;
    }

    switch ($_POST['transaction_type']) {
        case "self":
            handleSelfTransaction();
            break;
        case "slave":
            handleSlaveTransaction();
            break;
        default:
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(array("success" => false, "message" => "Invalid transaction_type"));
            break;
    }

    exit;

    function handleSlaveTransaction() {
        $connection = mysqli_connect(DB_FQDN, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if ( mysqli_connect_errno() ) {
            exit('Failed to connect to MySQL: ' . mysqli_connect_error());
        }

        if ($stmt = $connection->prepare('INSERT INTO Metropolis.methTransactions (playerid, methAmount, priceStacked, priceSingular, methAmountZakje, omzetZakje, percentageWitwas) VALUES(?, ?, ?, ?, ?, ?, ?);
')) {
            $playerid = (int)$_POST["playerid"];
            $methAmount = (int)$_POST['methAmount'];
            $priceStacked = (float)$_POST['priceStacked'];
            $priceSingular = (float)$_POST['priceSingular'];
            $methAmountZakje = (int)$_POST['methAmountZakje'];
            $omzetZakje = (int)$_POST['omzetZakje'];
            $percentageWitwas = (int)$_POST['percentageWitwas'];
            $stmt->bind_param('iiddiii', $playerid, $methAmount, $priceStacked, $priceSingular, $methAmountZakje, $omzetZakje, $percentageWitwas);
            $stmt->execute();
            $stmt->close();
        }

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(array("success" => true, "message" => "Successfully created transaction"));
    }

    function handleSelfTransaction() {
        $connection = mysqli_connect(DB_FQDN, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if ( mysqli_connect_errno() ) {
            exit('Failed to connect to MySQL: ' . mysqli_connect_error());
        }

        if ($stmt = $connection->prepare('INSERT INTO Metropolis.methTransactions (playerid, methAmount, methAmountZakje, omzetZakje, percentageWitwas) VALUES(?, ?, ?, ?, ?)')) {
            $methAmount = (int)$_POST['methAmount'];
            $methAmountZakje = (int)$_POST['methAmountZakje'];
            $omzetZakje = (int)$_POST['omzetZakje'];
            $percentageWitwas = (int)$_POST['percentageWitwas'];
            $stmt->bind_param('iiiii', $_SESSION['user']->id, $methAmount, $methAmountZakje, $omzetZakje, $percentageWitwas);
            $stmt->execute();
            $stmt->close();
        }

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(array("success" => true, "message" => "Successfully created transaction"));
    }
