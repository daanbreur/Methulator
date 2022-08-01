<ul class="nav">
    <li style="float:left;"><a href="home.php">Methulator</a></li>
    <li><a href="logout.php">Logout</a></li>
    <li><a href="profile.php"><?=$_SESSION['user']->username?> (PlayerID: <?=$_SESSION['user']->player->id?>)</a></li>
    <?php if ($_SESSION['user']->canViewAllPlayers()): ?>
        <li><a href="customer.php">Players</a></li>
    <?php endif; ?>
    <?php if ($_SESSION['user']->canCreateMethTransaction()): ?>
        <li><a href="add.php">Nieuwe methTransaction</a></li>
    <?php endif; ?>
    <?php if ($_SESSION['user']->canViewAccounts() or $_SESSION['user']->canCreateAccount() or $_SESSION['user']->canDeleteAccount()): ?>
        <li><a href="accounts.php">Account Beheer</a></li>
    <?php endif; ?>
</ul>