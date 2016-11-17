<?php
require_once('config.php');

class Database
{
    private $db;
    private $error;
    private $stmt;
    
    public function __construct()
    {
        $dsn = DB_TYPE.':host='.DB_HOST.';port='.DB_PORT.';dbname=' . DB_NAME;
        $options = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        try 
        {
            $this->db = new PDO($dsn, DB_USER, DB_PASS, $options);
        }
        catch (PDOException $e) 
        {
            $this->error = $e->getMessage();
        }
    }

    private function query($query)
    {
        $this->stmt = $this->db->prepare($query);
    }

    private function bind($param, $value, $type = null)
    {
        if (is_null($type)) 
        {
            switch (true) 
            {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    private function execute()
    {
        return $this->stmt->execute();
    }

//USER
    public function isLogged($userName,$session)
    {
        $this->query('SELECT * FROM clenove WHERE userName = :userName AND session = :session');
        $this->bind(':userName', $userName);
        $this->bind(':session', $session);
        $this->execute();
        return $this->stmt->rowCount() > 0;
    }
    
    public function logout($userName)
    {
        $this->query('UPDATE clenove SET session = NULL WHERE userName = :userName');
        $this->bind(':userName', $userName);
        $this->execute();
    }
    
    public function login($userName,$password)
    {
        
        $this->query('SELECT passHash FROM clenove WHERE userName = :userName');
        $this->bind(':userName', $userName);
        $this->execute();
        $dbHash = $this->stmt->fetch(PDO::FETCH_OBJ)->passHash;

        if (password_verify($password,$dbHash))
        {
            $this->query('SELECT login(:userName) as session');
            $this->bind(':userName', $userName);
            $this->execute();

            return $this->stmt->fetch(PDO::FETCH_OBJ)->session;
        }
        else
            return null;
    }

    public function register($userName,$password,$memberId)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        echo $hash.'<br>';
        $this->query('SELECT register(:userName, :passHash, :memberId) as result');
        $this->bind(':userName', $userName);
        $this->bind(':passHash', $hash);
        $this->bind(':memberId', $memberId);
        $this->execute();

        return $this->stmt->fetch(PDO::FETCH_OBJ)->result;
    }

    public function getInfo($userName)
    {
        $this->query('SELECT jmeno as name, id FROM clenove WHERE userName = :userName');
        $this->bind(':userName', $userName);
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

// MEMBERS
    public function getMembers()
    {

    }

    public function getMember($memberId)
    {

    }

    public function updateMember($member)
    {

    }

    public function addMember($member)
    {

    }

    public function removeMember($member)
    {

    }

//GAMES
    public function getGames()
    {

    }

    public function getGame($gameId)
    {

    }

    public function updateGame($game)
    {

    }

    public function addGame($game)
    {

    }

    public function removeGame($game)
    {

    }

//LOANS
    public function getLoans($memberId,$gameId)
    {

    }

    public function getLoan($loanId)
    {

    }

    public function updateLoan($loan)
    {

    }

    public function addLoadn($loan)
    {

    }
}

?>
