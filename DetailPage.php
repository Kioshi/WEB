<?php
require_once('Page.php');
require_once('Database.php');
require_once('User.php');

abstract class DetailPage extends Page
{
    protected $user;
    protected $db;
    protected $template;
    protected $formTemplate;
    protected $id;
    protected $type;

    public function __construct($user, $db, $twig, $id)
    {
        parent::__construct($twig);

        $this->db = $db;    
        $this->user = $user;
        $this->id = $id;
        $this->template = $twig->loadTemplate('detail.htm');
        $this->formTemplate = $twig->loadTemplate('loanForm.htm');

        $this->processPOST();
    }
    
    private function processPOST()
    {
        $this->type = 0;        
        // Process POST request and set type to id according to request
        if (isSet($_POST["formLoan"]))
        {
            if ($this->db->checkLoan($_POST))
            {
                if ($this->db->addLoan($_POST))
                    $this->type = 5;
                else
                    $this->type = 6;
            }
            else
                $this->type = 7;
        }

        // Process POST edit request
        if (isSet($_POST['pk']) && isSet($_POST['name']) && isSet($_POST['value']) && isSet($_POST['oldValue']))
        {
            $pk = $_POST['pk'];
            $name = $_POST['name'];
            $value = $_POST['value'];
            $oldValue = $_POST['oldValue'];

            /*
            Check submitted value
            */
            if(empty($value)) 
            {
                header('HTTP/1.0 400 Bad Request', true, 400);
                die;
            }
            else if(!$this->canEdit()) 
            {
                header('HTTP/1.0 401 Unauthorized', true, 401);
                die;
            }
            else 
            {
                $index = strpos($name,"_");
                if ($index === false)
                {
                    header('HTTP/1.0 400 Bad Request', true, 400);
                    die;
                }

                $table = substr($name,0,$index);
                $column = substr($name,$index+1);

                $res = false;
                if ($column != 'hraci')
                    $res = $this->db->updateDetail($table, $column, $pk, $value, $oldValue);
                else
                {
                    $index = strpos($value,"-");
                    if ($index === false)
                    {
                        header('HTTP/1.0 400 Bad Request', true, 400);
                        die;
                    }

                    $min = intval(substr($value,0,$index));
                    $max = intval(substr($value,$index+1));
                    if ($min >= 1 && $max >= 1 && $min <= $max)
                        $res = $this->db->updatePlayers($pk, $min, $max);
                }

                if ($res !== true)
                {
                    header('HTTP/1.0 400 Bad Request', true, 400);
                    die;
                }
            }
        }
    }

    // Abstract function determining if user can edit element
    abstract protected function canEdit();

    // Create data in correct format for detail.htm template
    abstract protected function makeInfo($data);

    // If user can edit returns array with editable a wraped around value, else returns simple formate value
    protected function makeEditable($name, $type, $key, $value)
    {
        if ($this->canEdit())
            return [$name => '<a href="#" class="editable" data-type="'.$type.'" data-pk="'.$this->id.'" data-name="'.$key.'">'.$value.'</a>'];
        else
        {
            switch($key)
            {
                case 'hry_cena':
                    return [$name => $value.' Kč'];
                case 'hry_hernidoba':
                    return [$name => $value.' min'];
                default:
                    return [$name => $value];
            }
        }
    }
}

class MemberPage extends DetailPage
{
    
    public function render()
    {
        $dbData = $this->db->getMember($this->id);
        $data = $this->makeInfo($dbData);
        $template_params = array();
        $template_params["pageName"] = 'Člen';
        $template_params["info"] = $data;
        $result = $this->template->render($template_params);
        if ($this->canEdit())
            $result .= $this->loanForm($dbData['jmeno']);
        
        return $result;
    }

    // Loads loan form to allow member create loans from their profile
    private function loanForm($name)
    {        
        $template_params = array();
        $template_params["type"] = $this->type;
        $closets = $this->db->getClosets();
        $template_params["closets"] = $closets;
        $template_params["closetsLenght"] = sizeof($closets);
        $members = array(array('nazev'=>$name, 'id' => $this->id));
        $template_params["members"] = $members;
        $template_params["membersLenght"] = sizeof($members);
        $games = $this->db->getGames();
        $template_params["games"] = $games;
        $template_params["gamesLenght"] = sizeof($games);
        return $this->formTemplate->render($template_params);
    }

    // Create data in correct format for detail.htm template
    protected function makeInfo($data)
    {
        $info = array();
        $info += $this->makeEditable('name','text','clenove_jmeno',$data['jmeno']);
        $info['data'] = array();
        if ($data)
        {
            foreach ($data as $key => $value)
            {
                switch($key)
                {
                    case 'img':
                        $info['img'] = $value;
                        break;
                    case 'prezdivka':
                        $info['data'] += $this->makeEditable('Přezdívka:', 'text', 'clen_'.$key, $value);
                        break;
                    case 'aktivni':
                        $info['data'] += $this->makeEditable('Aktivní:', 'text', 'clen_'.$key, $value);
                        break;
                    
                }
            }
        }
        return $info;
    }
    

    protected function canEdit()
    {
        return ($this->user->isLogged() && ($this->user->getInfo()['id'] == $this->id || $this->user->getRole() == 'ADMIN'));
    }
}

class GamePage extends DetailPage
{
    
    public function render()
    {
        $dbData = $this->db->getGame($this->id);
        $data = $this->makeInfo($dbData);
        $template_params = array();
        $template_params["pageName"] = 'Hra';
        $template_params["info"] = $data;
        return $this->template->render($template_params);
    }

    protected function canEdit()
    {
        return ($this->user->isLogged() && $this->user->getRole() == 'ADMIN');
    }

    protected function makeInfo($data)
    {
        $info = array();
        $info += $this->makeEditable('name','text','hry_nazev',$data['nazev']);
        $info['data'] = array();
        $table = 'hry_';
        foreach ($data as $key => $value)
        {
            switch($key)
            {
                case 'img':
                    $info['img'] = $value;
                    break;
                case 'alternativniNazev':
                    $info['data'] += $this->makeEditable('Alternativní název:', 'text', $table.$key, $value);
                    break;
                case 'cena':
                    $info['data'] += $this->makeEditable('Cena:', 'text', $table.$key, $value);
                    break;
                case 'datumPorizeni':
                    $info['data'] += $this->makeEditable('Datum pořízení:', 'text', $table.$key, $value);//, 'data-viewformat="dd.mm.yyyy"'
                    break;
                case 'zpusob':
                    $info['data'] += $this->makeEditable('Způsob pořízení:', 'text', $table.$key, $value);
                    break;
                case 'pozn':
                    $info['data'] += $this->makeEditable('Poznámka:', 'textarea', $table.$key, $value);
                    break;
                case 'link':
                    $info['data'] += $this->makeEditable('Odkaz:', 'text', $table.$key, $value);
                    break;
                case 'hernidoba':
                    $info['data'] += $this->makeEditable('Herní doba:', 'text', $table.$key, $value);
                    break;
                case 'hraci':
                    $info['data'] += $this->makeEditable('Počet hráčů:', 'text', $table.$key, $value);
                    break;
            }
        }
        return $info;
    }   
}

?>
