<?php

/**
 * Implements Account logic. Login, Password Change, Permissions
 */
class Player
{
    /**
     * @var int
     */
    public $id = null;
    /**
     * @var string
     */
    public $socialsecuritynumber = null;
    /**
     * @var string
     */
    public $firstname = null;
    /**
     * @var string
     */
    public $lastname = null;
    /**
     * @var string
     */
    public $phonenumber = null;
    /**
     * @var string
     */
    public $gender = null;
    /**
     * @var string
     */
    public $iban = null;
    /**
     * @var int
     */
    public $accountid = null;
    /**
     * @var int
     */
    public $gangid = null;
    /**
     * @var boolean
     */
    public $slave = False;

    /**
     *
     */
    public function __construct($id)
    {
        $this->id = $id;

        $connection = mysqli_connect(DB_FQDN, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if ( mysqli_connect_errno() ) {
            exit('Failed to connect to MySQL: ' . mysqli_connect_error());
        }

        if ($stmt = $connection->prepare('SELECT players.socialsecuritynumber, players.firstname, players.lastname, players.phonenumber, players.gender, players.gangid, players.iban, players.accountid, players.slave FROM players WHERE players.id = ?')) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->free_result();
            $stmt->close();

            $data = $result->fetch_array(MYSQLI_ASSOC);

            if (isset($data['socialsecuritynumber'])) $this->socialsecuritynumber = $data['socialsecuritynumber'];
            if (isset($data['firstname'])) $this->firstname = $data['firstname'];
            if (isset($data['lastname'])) $this->lastname = $data['lastname'];
            if (isset($data['phonenumber'])) $this->phonenumber = $data['phonenumber'];
            if (isset($data['gender'])) $this->gender = $data['gender'];
            if (isset($data['gangid'])) $this->gangid = $data['gangid'];
            if (isset($data['iban'])) $this->iban = $data['iban'];
            if (isset($data['accountid'])) $this->accountid = $data['accountid'];
            if (isset($data['slave'])) $this->slave = (bool)$data['slave'];
        }
    }

}