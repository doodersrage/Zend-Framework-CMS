<?PHP
// application/models/FormFields.php
 
class Application_Model_FormFields
{
    protected $_id;
    protected $_form_id;
    protected $_name;
    protected $_description;
    protected $_type;
    protected $_width;
    protected $_height;
    protected $_default_val;
    protected $_required;
    protected $_order_val;
 
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
 
    public function setForm_id($form_id)
    {
        $this->_form_id = (int) $form_id;
        return $this;
    }
 
    public function getForm_id()
    {
        return $this->_form_id;
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
 
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }
 
    public function getType()
    {
        return $this->_type;
    }
 
    public function setWidth($width)
    {
        $this->_width = (int)$width;
        return $this;
    }
 
    public function getWidth()
    {
        return $this->_width;
    }
 
    public function setHeight($height)
    {
        $this->_height = (int)$height;
        return $this;
    }
 
    public function getHeight()
    {
        return $this->_height;
    }
 
    public function setDefault_val($default_val)
    {
        $this->_default_val = $default_val;
        return $this;
    }
 
    public function getDefault_val()
    {
        return $this->_default_val;
    }
 
    public function setRequired($required)
    {
        $this->_required = (int)$required;
        return $this;
    }
 
    public function getRequired()
    {
        return $this->_required;
    }
 
    public function setOrder_val($order_val)
    {
        $this->_order_val = (int)$order_val;
        return $this;
    }
 
    public function getOrder_val()
    {
        return $this->_order_val;
    }
}
?>