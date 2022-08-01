<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\WebProcessor;

/**
 * Implements Account logic. Login, Password Change, Permissions
 */
class Account
{

    /**
     * @var bool
     */
    public $authenticated = False;
    /**
     * @var int
     */
    public $id = null;
    /**
     * @var string
     */
    public $username = null;
    /**
     * @var string
     */
    public $password = null;
    /**
     * @var int
     */
    public $permissionFlags = null;
    /**
     * @var boolean
     */
    public $isSuperAdmin = False;
    /**
     * @var Player
     */
    public $player = null;

    /**
     * Indicates if the user has the `PermissionFlag::VIEW_METH_TRANSACTION` flag set.
     *
     * @return bool
     */
    public function canViewMethTransaction(): bool
    { return $this->permissionFlags & PermissionFlag::VIEW_METH_TRANSACTION || $this->isSuperAdmin; }

    /**
     * Indicates if the user has the `PermissionFlag::CREATE_METH_TRANSACTION` flag set.
     *
     * @return bool
     */
    public function canCreateMethTransaction(): bool
    { return $this->permissionFlags & PermissionFlag::CREATE_METH_TRANSACTION || $this->isSuperAdmin; }

    /**
     * Indicates if the user has the `PermissionFlag::DELETE_METH_TRANSACTION` flag set.
     *
     * @return bool
     */
    public function canDeleteMethTransaction(): bool
    { return $this->permissionFlags & PermissionFlag::DELETE_METH_TRANSACTION || $this->isSuperAdmin; }

    /**
     * Indicates if the user has the `PermissionFlag::VIEW_PLAYER` flag set.
     *
     * @return bool
     */
    public function canViewPlayer(): bool
    { return $this->permissionFlags & PermissionFlag::VIEW_PLAYER || $this->isSuperAdmin; }

    /**
     * Indicates if the user has the `PermissionFlag::VIEW_ALL_PLAYERS` flag set.
     *
     * @return bool
     */
    public function canViewAllPlayers(): bool
    { return $this->permissionFlags & PermissionFlag::VIEW_ALL_PLAYERS || $this->isSuperAdmin; }

    /**
     * Indicates if the user has the `PermissionFlag::DELETE_PLAYER` flag set.
     *
     * @return bool
     */
    public function canDeletePlayer(): bool
    { return $this->permissionFlags & PermissionFlag::DELETE_PLAYER || $this->isSuperAdmin; }

    /**
     * Indicates if the user has the `PermissionFlag::DELETE_ALL_PLAYERS` flag set.
     *
     * @return bool
     */
    public function canDeleteAllPlayers(): bool
    { return $this->permissionFlags & PermissionFlag::DELETE_ALL_PLAYERS || $this->isSuperAdmin; }

    /**
     * Indicates if the user has the `PermissionFlag::VIEW_ACCOUNTS` flag set.
     *
     * @return bool
     */
    public function canViewAccounts(): bool
    { return $this->permissionFlags & PermissionFlag::VIEW_ACCOUNTS || $this->isSuperAdmin; }

    /**
     * Indicates if the user has the `PermissionFlag::CREATE_ACCOUNT` flag set.
     *
     * @return bool
     */
    public function canCreateAccount(): bool
    { return $this->permissionFlags & PermissionFlag::CREATE_ACCOUNT || $this->isSuperAdmin; }

    /**
     * Indicates if the user has the `PermissionFlag::DELETE_ACCOUNT` flag set.
     *
     * @return bool
     */
    public function canDeleteAccount(): bool
    { return $this->permissionFlags & PermissionFlag::DELETE_ACCOUNT || $this->isSuperAdmin; }

    /**
     *
     */
    public function __construct()
    {

    }

    /**
     * Reloads data from the databases and stores them in the `$this` variables.
     *
     * @return void
     */
    public function update()
    {
        if ($this->authenticated) {
            $logger = new Logger('Account', [new StreamHandler('app.log', Logger::DEBUG)], [new WebProcessor(null, ['url', 'ip', 'http_method', 'server', 'referrer', 'proxy_ip' => 'HTTP_X_FORWARDED_FOR'])]);

            $connection = mysqli_connect(DB_FQDN, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
            if ( mysqli_connect_errno() ) {
                exit('Failed to connect to MySQL: ' . mysqli_connect_error());
            }

            if ($stmt = $connection->prepare('SELECT accounts.username, accounts.password, accounts.isSuperAdmin, accounts.permissionFlags FROM accounts WHERE accounts.id = ?')) {
                $stmt->bind_param('i', $this->id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->free_result();
                $stmt->close();

                $data = $result->fetch_array(MYSQLI_ASSOC);

                $this->username = $data['username'];
                $this->password = $data['password'];
                $this->permissionFlags = $data['permissionFlags'];
                $this->isSuperAdmin = (bool)$data['isSuperAdmin'];

                $logger->info("Successfully updated account information", ['username' => $this->username, 'id' => $this->id]);
            }

            if ($stmt = $connection->prepare('SELECT players.id FROM players WHERE players.accountid = ?')) {
                $stmt->bind_param('i', $this->id);
                $stmt->execute();
                $stmt->bind_result($playerid);
                $stmt->fetch();
                $stmt->close();

                $this->player = new Player($playerid);
            }
        }
    }

    /**
     * Tries to log-in using specified username and password.
     *
     * If successful, `$this->authenticated` will be set to True and fetches all data from the database. Else return an error.
     *
     * @param string $inputUsername
     * @param string $inputPassword
     *
     * @return array|void
     */
    public function login(string $inputUsername, string $inputPassword)
    {
        $logger = new Logger('Account Security', [new StreamHandler('app.log', Logger::DEBUG)], [new WebProcessor(null, ['url', 'ip', 'http_method', 'server', 'referrer', 'proxy_ip' => 'HTTP_X_FORWARDED_FOR'])]);

        $connection = mysqli_connect(DB_FQDN, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if ( mysqli_connect_errno() ) {
            exit('Failed to connect to MySQL: ' . mysqli_connect_error());
        }

        if ($stmt = $connection->prepare('SELECT accounts.id, accounts.username, accounts.password, accounts.permissionFlags FROM accounts WHERE accounts.username = ?')) {
            $stmt->bind_param('s', $inputUsername);
            $stmt->execute();
            $stmt->store_result();

            $logger->info("Trying to authenticate user ", ['username' => $inputUsername]);
            if ($stmt->num_rows > 0){
                $stmt->bind_result($id, $dbUsername, $dbPassword, $permissionFlags);
                $stmt->fetch();
                $stmt->close();

                if (password_verify($inputPassword, $dbPassword)) {
                    session_regenerate_id();

                    $this->authenticated = True;
                    $this->id = $id;
                    $this->username = $dbUsername;
                    $this->password = $dbPassword;
                    $this->permissionFlags = $permissionFlags;

                    $logger->info("Successfully authenticated user ", ['username' => $inputUsername]);
                    return array('success' => true, "message" => "Successvol ingelogd.");
                } else {
                    $logger->info("Failed to authenticate user. Incorrect password for user", ['username' => $inputUsername]);
                    return array('success' => false, "message" => ErrorCodes::LOGIN_PASSWORD_INCORRECT);
                }
            } else {
                $logger->info("Failed to authenticate user. Unknown username", ['username' => $inputUsername]);
                return array('success' => false, "message" => ErrorCodes::LOGIN_USERNAME_NOTFOUND);
            }
        }

        return array('success' => false, "message" => "Something went wrong");
    }

    /**
     * Tries to change password for currently logged-in user.
     *
     * Checks if the entered `oldPassword` is the same as `$this->password`.
     * Checks if the `newPassword` and `confirmPassword` inputs are equal.
     * If checks pass, update password in database. Else return an error.
     *
     * @param string $oldPassword
     * @param string $newPassword
     * @param string $confirmPassword
     *
     * @return array|void
     */
    public function updatePassword(string $oldPassword, string $newPassword, string $confirmPassword)
    {
        $logger = new Logger('Account Security', [new StreamHandler('app.log', Logger::DEBUG)], [new WebProcessor(null, ['url', 'ip', 'http_method', 'server', 'referrer', 'proxy_ip' => 'HTTP_X_FORWARDED_FOR'])]);

        if ($this->authenticated) {
            $connection = mysqli_connect(DB_FQDN, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
            if ( mysqli_connect_errno() ) {
                exit('Failed to connect to MySQL: ' . mysqli_connect_error());
            }

            $logger->info("Trying to change user password", ['username' => $this->username, 'id' => $this->id]);
            if (password_verify($oldPassword, $this->password)) {
                if ($newPassword == $confirmPassword) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $connection->prepare('UPDATE accounts SET password=? WHERE id=?');
                    $stmt->bind_param('si', $hashedPassword, $this->id);
                    $stmt->execute();
                    $stmt->free_result();
                    $stmt->close();
                    $this->password = $hashedPassword;
                    $logger->info("Successfully changed password.", ['username' => $this->username, 'id' => $this->id]);
                    return array('success' => true, "message" => "Wachtwoord successvol veranderd.");
                } else {
                    $logger->info("Failed to change password. New passwords dont match", ['username' => $this->username, 'id' => $this->id]);
                    return array('success' => false, "message" => ErrorCodes::CHANGE_PASSWORD_NEWPASSWORDS_DIFFERENT);
                }
            } else {
                $logger->info("Failed to change password. Current password incorrect", ['username' => $this->username, 'id' => $this->id]);
                return array('success' => false, "message" => ErrorCodes::CHANGE_PASSWORD_INCORRECT_CURRENT);
            }
        } else {
            return array('success' => false, "message" => ErrorCodes::CHANGE_PASSWORD_NOT_AUTHENTICATED);
        }
    }


    public function generateJwt(): array
    {
        if (!$this->authenticated) {
            return array("success" => false, "token" => "", "message" => "Not authenticated");
        }

        $payload = [
            'accountid' => $this->id,
        ];

        $jwt = JWT::encode($payload, JWT_KEY, 'HS256');

        return array("success" => true, "token" => $jwt, "message" => "Successfully generated JWT-token");
    }

    public function authenticateJwt(string $jwt): array
    {
        try {
            $decoded = (array)JWT::decode($jwt, new Key(JWT_KEY, 'HS256'));

            $this->authenticated = true;
            $this->id = $decoded['accountid'];
            $this->update();

            return array('success' => false, "message" => "Successfully authenticated using JWT-token");
        } catch (SignatureInvalidException $e) {
            return array('success' => false, "message" => ErrorCodes::LOGIN_JWT_SIGNATURE_INVALID);
        }
    }

}