<?php
require_once "config.php";

session_start();
if (!isset($_SESSION['user'])) $_SESSION['user'] = new Account();
if (!$_SESSION['user']->authenticated) {
    header('Location: login.php');
    exit;
}

$_SESSION['user']->update();
$title = 'Maak methTransaction';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/nav.php'; ?>
<div class="content">
    <?php if (isset($error)): ?>
        <div><p id='error-msg'><?= $error ?></p></div>
    <?php else: ?>
        <h2>My Transaction</h2>
        <div>
            <form method="post" action="/api/add_transaction.php">
                <input type="hidden" name="transaction_type" value="self"/>
                Aantal meth: <input type="number" name="methAmount" /><br/>
                Aantal meth per zakje: <input type="number" value="3" name="methAmountZakje" /><br/>
                Omzet per zakje meth: <input type="number" value="450" name="omzetZakje" /><br/>
                Witwas percentage: <input type="number" value="10" min="0" max="100" name="percentageWitwas" /><br/>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
        <h2>Slave Transaction</h2>
        <div>
            <form method="post" action="/api/add_transaction.php">
                <input type="hidden" name="transaction_type" value="slave"/>
                Player id: <select name="playerid">
                <?php
                    $connection = mysqli_connect(DB_FQDN, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
                    if ( mysqli_connect_errno() ) {
                        exit('Failed to connect to MySQL: ' . mysqli_connect_error());
                    }

                    $stmt = $connection->prepare("SELECT id, firstname, lastname FROM Metropolis.players");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->free_result();
                    $stmt->close();

                    while($row = $result->fetch_assoc()) {
                        echo "<option value='".$row['id']."'>".$row['firstname']." ".$row['lastname']." (ID: ".$row['id'].")"."</option>";
                    }
                ?>
                </select><br/>
                Aantal meth: <input type="number" name="methAmount" /><br/>
                Prijs per 300 meth: <input type="number" value="20000" name="priceStacked" /><br/>
                Prijs per losse meth: <input type="number" value="55" name="priceSingular" /><br/>
                Aantal meth per zakje: <input type="number" value="3" name="methAmountZakje" /><br/>
                Omzet per zakje meth: <input type="number" value="450" name="omzetZakje" /><br/>
                Witwas percentage: <input type="number" value="10" min="0" max="100" name="percentageWitwas" /><br/>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>