<?PHP
// application/models/Inventory.php
 
class Application_Model_Inventory
{
    protected $_stockNum;
    protected $_year;
    protected $_make;
    protected $_model;
    protected $_style;
    protected $_color;
    protected $_vin;
    protected $_images;
    protected $_name;
    protected $_price;
    protected $_desciption;
 
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
 
    public function setStockNum($stockNum)
    {
        $this->_stockNum = (int) $stockNum;
        return $this;
    }
 
    public function getStockNum()
    {
        return $this->_stockNum;
    }
  
    public function setYear($year)
    {
        $this->_year = (int) $year;
        return $this;
    }
 
    public function getYear()
    {
        return $this->_year;
    }
 
    public function setMake($make)
    {
        $this->_make = (string) $make;
        return $this;
    }
 
    public function getMake()
    {
        return $this->_make;
    }
 
    public function setModel($model)
    {
        $this->_model = $model;
        return $this;
    }
 
    public function getModel()
    {
        return $this->_model;
    }
 
    public function setStyle($style)
    {
        $this->_style = $style;
        return $this;
    }
 
    public function getStyle()
    {
        return $this->_style;
    }
 
    public function setColor($color)
    {
        $this->_color = $color;
        return $this;
    }
 
    public function getColor()
    {
        return $this->_color;
    }
 
    public function setVin($vin)
    {
        $this->_vin = $vin;
        return $this;
    }
 
    public function getVin()
    {
        return $this->_vin;
    }
 
    public function setImages($images)
    {
        $this->_images = $images;
        return $this;
    }
 
    public function getImages()
    {
        return $this->_images;
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
 
    public function setPrice($price)
    {
        $this->_price = $price;
        return $this;
    }
 
    public function getPrice()
    {
        return $this->_price;
    }
 
    public function setDesciption($desciption)
    {
        $this->_desciption = $desciption;
        return $this;
    }
 
    public function getDesciption()
    {
        return $this->_desciption;
    }
}
?>