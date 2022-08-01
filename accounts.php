<?php
require_once "config.php";

session_start();
if (!isset($_SESSION['user'])) $_SESSION['user'] = new Account();
if (!$_SESSION['user']->authenticated) {
    header('Location: login.php');
    exit;
}

$_SESSION['user']->update();
$title = 'Account Beheer';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/nav.php'; ?>
<div class="content">
    <?php if (isset($error)): ?>
        <div><p id='error-msg'><?= $error ?></p></div>
    <?php elseif (isset($_GET['aid'])): ?>
    <?php else: ?>
        <h2>Accounts</h2>
        <div>
            <?php if ($_SESSION['user']->canViewAccounts()): ?>
                <table class="highlighted">
                    <tr><th>Id</th><th>Username</th><th>0--</th><th>--</th><th>--</th><th>Info Pagina</th></tr>
                    <?php
                    $connection = mysqli_connect(DB_FQDN, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
                    if ( mysqli_connect_errno() ) {
                        exit('Failed to connect to MySQL: ' . mysqli_connect_error());
                    }
                    if ($stmt = $connection->prepare("SELECT c.* FROM customers c ORDER BY lastname ASC")) {
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $stmt->free_result();
                        $stmt->close();
                        while($row = $result->fetch_assoc()) {
                            ?>
                            <tr><td><?=$row['id']?></td><td><?=$row['-']?></td><td><?=$row['-']?></td><td><?=$row['-']?></td><td><?=$row['-']?></td><td><a href='accounts.php?id=<?=$row['id']?>'>link</a></td></tr>
                            <?php
                        }
                    }
                    ?>
                </table>
            <?php else: ?>
                <p id='error-msg'><?= ErrorCodes::PERMISSION_CANT_SHOW_ACCOUNTS ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php include "includes/footer.php" ?>
