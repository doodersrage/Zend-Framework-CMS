<?PHP
// application/models/Forms.php
 
class Application_Model_Forms
{
    protected $_id;
    protected $_name;
    protected $_description;
    protected $_email;
    protected $_message;
    protected $_captcha;
 
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
  
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }
 
    public function getName()
    {
        return $this->_name;
    }
 
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }
 
    public function getDescription()
    {
        return $this->_description;
    }
 
    public function setEmail($email)
    {
        $this->_email = $email;
        return $this;
    }
 
    public function getEmail()
    {
        return $this->_email;
    }
 
    public function setMessage($message)
    {
        $this->_message = $message;
        return $this;
    }
 
    public function getMessage()
    {
        return $this->_message;
    }
 
    public function setCaptcha($captcha)
    {
        $this->_captcha = $captcha;
        return $this;
    }
 
    public function getCaptcha()
    {
        return $this->_captcha;
    }
}
?>