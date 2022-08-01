<?php
require_once "config.php";

session_start();
if (!isset($_SESSION['user'])) $_SESSION['user'] = new Account();
if (!$_SESSION['user']->authenticated) {
    header('Location: login.php');
    exit;
}

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    if (!isset($_POST['currentPassword']) ) {
        $error = ErrorCodes::CHANGE_PASSWORD_CURRENT_NOT_ENTERED;
    } else if (!isset($_POST['newPassword']) ) {
        $error = ErrorCodes::CHANGE_PASSWORD_NEW_NOT_ENTERED;
    } else if (!isset($_POST['confirmPassword']) ) {
        $error = ErrorCodes::CHANGE_PASSWORD_CONFIRM_NOT_ENTERED;
    } else {
        $result = $_SESSION['user']->updatePassword($_POST['currentPassword'], $_POST['newPassword'], $_POST['confirmPassword']);
        $error = $result['message'];
    }
}

$_SESSION['user']->update();
$title = 'Account';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/nav.php'; ?>
<div class="content">
        <h2>Account Informatie</h2>
        <div>
            <p>Jouw account informatie staat hieronder beschreven:</p>
            <table>
                <tr>
                    <td>Username:</td>
                    <td><?=$_SESSION['user']->username?></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><?=$_SESSION['user']->password?></td>
                </tr>
                <tr>
                    <td>Administrator:</td>
                    <td><?=$_SESSION['user']->isSuperAdmin?'True':'False'?></td>
                </tr>
                <tr>
                    <td>JWT-Token:</td>
                    <td><?=$_SESSION['user']->generateJwt()['token']?></td>
                </tr>
            </table>
        </div>
        <h2>Account Permissies</h2>
        <div>
            <p>Jouw account permissies zijn hieronder beschreven:</p>
            <table>
                <?php foreach (PermissionFlag::toArray() as $key => $value) { ?>
                    <tr><td><?=$key?>:</td><td><?=($_SESSION['user']->permissionFlags&$value)?'True':'False'?></td></tr>
                <?php } ?>
            </table>
        </div>
        <h2>Wachtwoord Veranderen</h2>
        <div>
            <form action="profile.php" method="post">
                <p id="error-msg"><?php echo $error ?? ''; ?></p>
                <input type="password" name="currentPassword" placeholder="Current Password" id="currentPassword" required>
                <input type="password" name="newPassword" placeholder="New Password" id="newPassword" required>
                <input type="password" name="confirmPassword" placeholder="Confirm Password" id="confirmPassword" required>
                <input type="submit" value="Change">
            </form>
        </div>
</div>
<?php include 'includes/footer.php'; ?>