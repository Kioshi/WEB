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
        $result = $this->stmt->fetch(PDO::FETCH_OBJ);
        $dbHash = $result ? $result->passHash : '';

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

    public function getRole($userName)
    {
        $this->query('SELECT r.role FROM clenove c JOIN role r ON r.id = c.role WHERE userName = :userName');
        $this->bind(':userName', $userName);
        $this->execute();
        return ($this->stmt->fetch()[0]);
        //return $this->stmt->fetch(PDO::FETCH_ASSOC)->rol;
    }

// MEMBERS
    public function getMembers()
    {
        $this->query('SELECT id, jmeno as nazev FROM clenove');
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMember($memberId)
    {
        $this->query('SELECT jmeno AS name, \'http://pingendo.github.io/pingendo-bootstrap/assets/placeholder.png\' as img, prezdivka AS \'Přezdívka:\', aktivni as \'Aktivní:\' FROM clenove WHERE id = :id');
        $this->bind(':id', $memberId);
        $this->execute();
        $array = $this->stmt->fetch(PDO::FETCH_ASSOC);
        $img = $array['img'];
        unset($array['img']);
        $name = $array['name'];
        unset($array['name']);
        return array('img' => $img, 'name' => $name, 'data' => $array);

    }

    public function updateMember($member)
    {

    }

    public function addMember($member)
    {
        $array = array();
        foreach ($member as $key => $value)
        {
            if (!$value || $value == '')
                continue;
            switch($key)
            {
                case 'jmeno': $array['jmeno'] = htmlspecialchars($value); break;
                case 'nick': $array['prezdivka'] = htmlspecialchars($value); break;
                case 'password': 
                    if (strlen($value) < 6) return false;
                    $array['passHash'] = password_hash($value, PASSWORD_DEFAULT);
                    break;
                case 'username': $array['userName'] = htmlspecialchars($value); break;
            }
        }
        $sql1 = 'INSERT INTO clenove (';
        $sql2 = ') VALUES (';
        $first = true;
        foreach ($array as $key => $value)
        {
            if ($first) $first = false;
            else
            {
                $sql1 .= ',';
                $sql2 .= ',';
            }
            $sql1 .= $key;
            $sql2 .= ':'.$key;
        }
        $this->query($sql1.$sql2.')');

        foreach ($array as $key => $value)
            $this->bind(':'.$key, $value);
        try 
        {
            return $this->execute();
        }
        catch( PDOException $Exception ) 
        {
            return false;
        }
    }

    public function removeMember($member)
    {

    }

//GAMES
    public function getGames()
    {
        $this->query('SELECT id, nazev FROM hry');
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGame($gameId)
    {
        $this->query('SELECT nazev AS name, \'http://pingendo.github.io/pingendo-bootstrap/assets/placeholder.png\' as img, alternativniNazev AS \'Alternativní název:\', CONCAT(cena, \' Kč\') AS \'Cena:\', datumPorizeni AS \'Datum pořízení:\', zpusob AS \'Způsob pořízení:\', pozn AS \'Poznámka:\', link AS \'Odkaz:\', CONCAT(hernidoba,\' min\') AS \'Herní doba:\', CONCAT(minPocet, "-", maxPocet, " hráčů") AS \'Počet hráčů:\' FROM hry WHERE id = :id');
        $this->bind(':id', $gameId);
        $this->execute();
        $array = $this->stmt->fetch(PDO::FETCH_ASSOC);
        $img = $array['img'];
        unset($array['img']);
        $name = $array['name'];
        unset($array['name']);
        return array('img' => $img, 'name' => $name, 'data' => $array);
    }

    public function updateGame($game)
    {

    }

    public function addGame($game)
    {
        $array = array();
        print_r($game);
        foreach ($game as $key => $value)
        {
            if (!$value || $value == '')
                continue;
            switch($key)
            {
                case 'nazev': $array['nazev'] = htmlspecialchars($value); break;
                case 'alter': $array['alternativniNazev'] = htmlspecialchars($value); break;
                case 'link': $array['link'] = htmlspecialchars($value); break;
                case 'price': $array['cena'] = $value; break;
                case 'date': $array['datumPorizeni'] = $value; break;
                case 'method': $array['zpusob'] = htmlspecialchars($value); break;
                case 'playtime': $array['hernidoba'] = $value; break;
                case 'min': $array['minPocet'] = $value; break;
                case 'max': $array['maxPocet'] = $value; break;
                case 'note': $array['pozn'] = htmlspecialchars($value); break;
                case 'skrin': $array['skrine'] = $value; break;
            }
        }
        $sql1 = 'INSERT INTO hry (';
        $sql2 = ') VALUES (';
        $first = true;
        foreach ($array as $key => $value)
        {
            if ($first) $first = false;
            else
            {
                $sql1 .= ',';
                $sql2 .= ',';
            }
            $sql1 .= $key;
            $sql2 .= ':'.$key;
        }
        $this->query($sql1.$sql2.')');

        foreach ($array as $key => $value)
            $this->bind(':'.$key, $value);
        
        try 
        {
            return $this->execute();
        }
        catch( PDOException $Exception ) 
        {
            return false;
        }
    }

    public function removeGame($game)
    {

    }

//LOANS
    public function getLoans($memberId,$gameId)
    {
        $sql = 'SELECT v.id, c.jmeno AS clen, h.nazev AS hra, v.datumPujceni, v.datumVraceni, v.pozn FROM vypujcky v JOIN hry h ON v.hry_id = h.id JOIN clenove c ON c.id = v.clenove_id';
        if ($memberId != null)
        {
            $sql .= ' WHERE v.clenove_id = :memberId';
            $this->query($sql);
            $this->bind(':memberId', $memberId);
        }
        else if ($gameId != null)
        {
            $sql .= ' WHERE v.hry_id = :gameId';
            $this->query($sql);
            $this->bind(':gameId', $gameId);
        }
        else
            $this->query($sql);
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateLoan($loan)
    {

    }

    public function addLoan($loan)
    {
        $this->query('INSERT INTO vypujcky (clenove_id, hry_id, datumPujceni, datumVraceni, pozn) VALUES (:clen,:hra, :od, :do, :note)');
        $this->bind(':hra', $loan['game']);
        $this->bind(':clen', $loan['user']);
        $this->bind(':od', $loan['od']);
        $this->bind(':do', $loan['do']);
        $this->bind(':note', htmlspecialchars($loan['note']));
        try 
        {
            return $this->execute();
        }
        catch( PDOException $Exception ) 
        {
            return false;
        }   

    }

    public function checkLoan($loan)
    {
        if (new DateTime($loan['od']) > new DateTime($loan['do'])) 
            return false;

        $this->query('SELECT * FROM vypujcky WHERE hry_id = :hra AND ((datumPujceni BETWEEN :od AND :do) OR (datumVraceni BETWEEN :od AND :do) OR (:od BETWEEN datumPujceni AND datumVraceni))');
        $this->bind(':hra', $loan['game']);
        $this->bind(':od', $loan['od']);
        $this->bind(':do', $loan['do']);
        $this->execute();
        return $this->stmt->rowCount() == 0;
    }

    // Closets
    public function addCloset($closet)
    {
        $this->query('INSERT INTO skrine (skrin) VALUES (:skrin)');
        $this->bind(':skrin', $closet['skrin']);
        try 
        {
            return $this->execute();
        }
        catch( PDOException $Exception ) 
        {
            return false;
        }   

    }

    public function getClosets()
    {
        $this->query('SELECT id, skrin FROM skrine');
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);        
    }
}

?>
