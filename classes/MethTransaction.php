<?php

/**
 * Implements Account logic. Login, Password Change, Permissions
 */
class MethTransaction
{
    /**
     * @var int
     */
    public $id = null;
    /**
     * @var int
     */
    public $playerid = null;
    /**
     * @var int
     */
    public $methAmount = null;
    /**
     * @var float
     */
    public $priceStacked = null;
    /**
     * @var float
     */
    public $priceSingular = null;
    /**
     * @var int
     */
    public $methAmountZakje = null;
    /**
     * @var int
     */
    public $omzetZakje = null;
    /**
     * @var int
     */
    public $percentageWitwas = null;
    /**
     * @var DateTime
     */
    public $transactionDate = null;

    /**
     *
     */
    public function __construct($id)
    {
        $this->id = $id;

        $connection = mysqli_connect(DB_FQDN, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if (mysqli_connect_errno()) {
            exit('Failed to connect to MySQL: ' . mysqli_connect_error());
        }

        if ($stmt = $connection->prepare('SELECT methTransactions.playerid, methTransactions.methAmount, methTransactions.priceStacked, methTransactions.priceSingular, methTransactions.methAmountZakje, methTransactions.omzetZakje, methTransactions.percentageWitwas, methTransactions.transactionDate FROM methTransactions WHERE methTransactions.id = ?')) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->free_result();
            $stmt->close();

            $data = $result->fetch_array(MYSQLI_ASSOC);

            $this->playerid = $data['playerid'];
            $this->methAmount = $data['methAmount'];
            $this->priceStacked = $data['priceStacked'];
            $this->priceSingular = $data['priceSingular'];
            $this->methAmountZakje = $data['methAmountZakje'];
            $this->omzetZakje = $data['omzetZakje'];
            $this->percentageWitwas = $data['percentageWitwas'];
            $this->transactionDate = new DateTime($data['transactionDate']);
        }
    }

    /**
     * Indicates if the transaction is made by a slave. Evaluated by if it has a stacked and singular meth price.
     *
     * @return bool
     */
    public function isSlaveTransaction(): bool
    {
        return ($this->priceSingular != null && $this->priceStacked != null);
    }

    /**
     * Calculates the revenue before money laundering.
     *
     * @return float
     */
    public function calculateOmzet(): float
    {
        return ($this->methAmount / $this->methAmountZakje) * $this->omzetZakje;
    }

    /**
     * Calculates the revenue after money laundering.
     *
     * @return float
     *
     * @uses MethTransaction::calculateOmzet()
     */
    public function calculateOmzetNaWitwas(): float
    {
        $omzetVoorWitwas = $this->calculateOmzet();
        return $omzetVoorWitwas - $omzetVoorWitwas * ($this->percentageWitwas / 100);
    }

    /**
     * Calculates the money for the slave using the rates for the transaction.
     * If called on a not slaveTransaction returns 0.0.
     *
     * @return float
     *
     * @uses MethTransaction::isSlaveTransaction()
     */
    public function calculateMoneyForSlave(): float
    {
        if (!$this->isSlaveTransaction()) return 0.0;

        $amountStacked = (int)floor(($this->methAmount / 300));
        $amountSingular = $this->methAmount - ($amountStacked * 300);

        return ($this->priceStacked * $amountStacked) + ($this->priceSingular * $amountSingular);
    }

    /**
     * Calculates the profit. Substracts money for the slave from revenue after money laundering.
     *
     * @return float
     *
     * @uses MethTransaction::calculateOmzetNaWitwas()
     * @uses MethTransaction::calculateMoneyForSlave()
     */
    public function calculateProfit(): float
    {
        return $this->calculateOmzetNaWitwas() - $this->calculateMoneyForSlave();
    }

    /**
     * Calculates the profitPercentage.
     *
     * @return float
     *
     * @uses MethTransaction::calculateProfit()
     * @uses MethTransaction::calculateOmzetNaWitwas()
     */
    public function calculateProfitPercentage(): float
    {
        $omzetNaWitwas = $this->calculateOmzetNaWitwas();
        $winst = $this->calculateProfit();
        $winstPercentage = 0;

        if ($omzetNaWitwas > 0 || $omzetNaWitwas < 0) {
            $winstPercentage = ($winst / ($omzetNaWitwas / 100));
        }

        return $winstPercentage;
    }
}