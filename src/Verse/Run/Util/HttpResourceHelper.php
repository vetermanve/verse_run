<?php


namespace Verse\Run\Util;


class HttpResourceHelper
{
    const R_1_PART_RESOURCE = 0;
    const R_1_PART_ITEM_ID  = 1;
    const R_1_PART_METHOD   = 2;
    
    const R_3_PART_TYPE     = 0;
    const R_3_PART_VERSION  = 1;
    const R_3_PART_RESOURCE = 2;
    
    const R_3_PART_ITEM_ID = 3;
    
    const TYPE_REST = 'rest';
    const TYPE_WEB  = 'web';
    const TYPE_READ = 'read';
    const TYPE_DEV  = 'dev';
    
    
    private $string   = '';
    private $resource = '';
    private $type;
    private $method;
    
    private static $subResourceTypes = [
        self::TYPE_REST => self::TYPE_REST,
        self::TYPE_WEB  => self::TYPE_WEB,
        self::TYPE_READ => self::TYPE_READ,
        self::TYPE_DEV => self::TYPE_DEV
    ];
    
    private $defaultType;
    
    private $id;
    
    /**
     * HttpResourceHelper constructor.
     *
     * @param            $string
     * @param string     $defaultType
     */
    public function __construct($string, $defaultType = self::TYPE_WEB)
    {
        $this->string      = $string;
        $this->defaultType = $defaultType;
        
        $this->_parse();
    }
    
    private function _parse()
    {
        $path = strpos($this->string, '?') ? strstr($this->string, '?', true) : $this->string;
        $data = explode('/', trim($path, '/'));
        
        if (count($data) > 1 && isset(self::$subResourceTypes[$data[0]])) {
            $this->type = array_shift($data);
        } else {
            $this->type = $this->defaultType;
        }
        
        if (count($data) === 1) { // /auth
            $this->resource = $data[self::R_1_PART_RESOURCE];
        } elseif (count($data) === 2) {  // /user/644
            $this->resource = $data[self::R_1_PART_RESOURCE];
            $this->id       = $data[self::R_1_PART_ITEM_ID];
        } elseif (count($data) > 2) { // /user/644/edit //web specific 
            $this->id       = $data[self::R_1_PART_ITEM_ID];
            $this->resource = $data[self::R_1_PART_RESOURCE];
            $this->method   = $data[self::R_1_PART_METHOD];
        }
    }
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }
}