<?PHP
// application/models/Users.php
 
class Application_Model_Users
{
    protected $_id;
    protected $_username;
    protected $_password;
    protected $_group;
    protected $_lastLogin;
 
    public function __construct()
    {
    }
 
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid table property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid table property');
        }
        return $this->$method();
    }
 
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }
 
    public function getId()
    {
        return $this->_id;
    }
  
    public function setUsername($username)
    {
        $this->_username = (string) $username;
        return $this;
    }
 
    public function getUsername()
    {
        return $this->_username;
    }
 
    public function setPassword($password)
    {
        $this->_password = (string) $password;
        return $this;
    }
 
    public function getPassword()
    {
        return $this->_password;
    }
 
    public function setGroup($group)
    {
        $this->_group = $group;
        return $this;
    }
 
    public function getGroup()
    {
        return $this->_group;
    }
 
    public function setLastLogin($lastLogin)
    {
        $this->_lastLogin = $lastLogin;
        return $this;
    }
 
    public function getLastLogin()
    {
        return $this->_lastLogin;
    }
}
?>