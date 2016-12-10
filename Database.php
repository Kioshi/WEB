<?php
require_once('config.php');

class Database
{
    private $db;
    private $error;
    private $stmt;
    
    // Create connection to DB
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

    // Creates statement
    private function query($query)
    {
        $this->stmt = $this->db->prepare($query);
    }

    // Binds paramters into statement
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

    // Execute statement
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
        $this->query('SELECT register(:userName, :passHash, :memberId) as result');
        $this->bind(':userName', $userName);
        $this->bind(':passHash', $hash);
        $this->bind(':memberId', $memberId);
        $this->execute();

        return $this->stmt->fetch(PDO::FETCH_OBJ)->result;
    }

    // Returns array with name and id of user
    public function getInfo($userName)
    {
        $this->query('SELECT jmeno as name, id FROM clenove WHERE userName = :userName');
        $this->bind(':userName', $userName);
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Returns user role
    public function getRole($userName)
    {
        $this->query('SELECT * FROM clenove');
        $this->execute();
        if ($this->stmt->rowCount() == 0)
            return 'ADMIN';
        $this->query('SELECT r.role FROM clenove c JOIN role r ON r.id = c.role WHERE userName = :userName');
        $this->bind(':userName', $userName);
        $this->execute();
        return ($this->stmt->fetch()[0]);
    }

// MEMBERS
    public function getMembers()
    {
        $this->query('SELECT id, jmeno as nazev, img  FROM clenove');
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMember($memberId)
    {
        $this->query('SELECT jmeno, img, prezdivka, aktivni FROM clenove WHERE id = :id');
        $this->bind(':id', $memberId);
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);

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
                case 'img': $array['img'] = htmlspecialchars($value); break;
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
        $sql = 'DELETE FROM clenove WHERE id = :id';
        $this->query($sql);
        $this->bind(':id', $member);
        try 
        {
            return $this->execute();
        }
        catch( PDOException $Exception ) 
        {
            return false;
        }
    }

    // Validate and update member/game
    public function updateDetail($table, $column, $id, $value, $oldValue)
    {
        switch($table)
        {
            case 'clenove':
                switch($column)
                {
                    case 'aktivni':
                    case 'jmeno':
                    case 'prezdivka':
                        break;
                    default: return false;
                }
                break;
            case 'hry':
                switch($column)
                {
                    case 'nazev':
                    case 'alternativniNazev':
                    case 'zpusob':
                    case 'pozn':
                    case 'link':
                        break;
                    case 'hernidoba':
                    case 'skrine':
                    case 'minPocet':
                    case 'maxPocet':
                    case 'cena':
                        break;
                    case 'datumPorizeni':
                        break;
                    default: return false;
                }
                break;
            default: return false;
        }
        $sql = 'UPDATE '.$table.' SET '.$column.' = :column WHERE id = :id AND '.$column.' = :old';
        $this->query($sql);
        $this->bind(':column', htmlspecialchars($value));
        $this->bind(':id', $id);
        $this->bind(':old', $oldValue);        
        try 
        {
            return $this->execute();
        }
        catch( PDOException $Exception ) 
        {
            return false;
        }   
    }

    // Update min and max players for game
    public function updatePlayers($id, $min, $max)
    {
        $sql = 'UPDATE hry SET minPocet = :min, maxPocet = :max WHERE id = :id';
        $this->query($sql);
        $this->bind(':id', $id);
        $this->bind(':min', $min);
        $this->bind(':max', $max);
        try 
        {
            return $this->execute();
        }
        catch( PDOException $Exception ) 
        {
            return false;
        }   
    }

//GAMES
    public function getGames()
    {
        $this->query('SELECT id, nazev, img FROM hry');
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGame($gameId)
    {
        $this->query('SELECT nazev, img, alternativniNazev, cena, datumPorizeni, zpusob, pozn, link, hernidoba, CONCAT(minPocet, "-", maxPocet) AS hraci FROM hry WHERE id = :id');
        $this->bind(':id', $gameId);
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addGame($game)
    {
        $array = array();
        foreach ($game as $key => $value)
        {
            if (!$value || $value == '')
                continue;
            switch($key)
            {
                case 'nazev': $array['nazev'] = htmlspecialchars($value); break;
                case 'alter': $array['alternativniNazev'] = htmlspecialchars($value); break;
                case 'link': $array['link'] = htmlspecialchars($value); break;
                case 'img': $array['img'] = htmlspecialchars($value); break;
                case 'price': $array['cena'] = $value; break;
                case 'date': $array['datumPorizeni'] = implode('-',array_reverse(explode('.',$value))); break;
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
        $sql = 'DELETE FROM hry WHERE id = :id';
        $this->query($sql);
        $this->bind(':id', $game);
        try 
        {
            return $this->execute();
        }
        catch( PDOException $Exception ) 
        {
            return false;
        }
    }

//LOANS
    public function getLoans($memberId = null,$gameId = null)
    {
        $sql = 'SELECT v.id, c.jmeno AS clen, c.id as clen_id, h.nazev AS hra, h.id as hra_id, v.datumPujceni, v.datumVraceni, v.pozn FROM vypujcky v JOIN hry h ON v.hry_id = h.id JOIN clenove c ON c.id = v.clenove_id';
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

    public function addLoan($loan)
    {
        $this->query('INSERT INTO vypujcky (clenove_id, hry_id, datumPujceni, datumVraceni, pozn) VALUES (:clen,:hra, :od, :do, :note)');
        $this->bind(':hra', $loan['game']);
        $this->bind(':clen', $loan['user']);
        $this->bind(':od', implode('-',array_reverse(explode('.',$loan['od']))));
        $this->bind(':do', implode('-',array_reverse(explode('.',$loan['do']))));
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

    // Determine if game is not already loaned in specified date
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

    public function removeLoan($loan)
    {
        $sql = 'DELETE FROM vypujcky WHERE id = :id';
        $this->query($sql);
        $this->bind(':id', $loan);
        try 
        {
            return $this->execute();
        }
        catch( PDOException $Exception ) 
        {
            return false;
        }
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
