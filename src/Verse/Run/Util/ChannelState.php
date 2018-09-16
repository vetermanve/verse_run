<?php


namespace Verse\Run\Util;


class ChannelState
{
    /* Cookies hack */
    const PACK_DELIMITER = '---';
    
    const ENCODE_JSON_SIMPLE = 'js';
    const ENCODE_CAT = 'c';
    const ENCODE_NONE = 'n';
    
    const DATA_ENCODING   = 0;
    const DATA_EXPIRATION = 1;
    const DATA_BODY       = 2;
    
    const DEFAULT_TTL = 2592000; // 30 дней  
    
    private $encoder = self::ENCODE_NONE;
    
    /**
     * Данные состояния канала
     * 
     * @var array 
     */
    private $data = [];
    
    /**
     * Какие куки были подписаны
     *
     * @var array
     */
    private $signed = [];
    
    /**
     * Время истечения валидности
     * 
     * @var array
     */
    private $expiresAt = [];
    
    /**
     * Запакованные данные
     * В значениях запакованные строки
     * 
     * @var array
     */
    private $packed = [];
    
    private $touched = [];
    
    public function unpack () 
    {
        foreach ($this->packed as $key => &$string) {
            $item = explode(self::PACK_DELIMITER, $string, 3);
            
            if (count($item) > 2 && $item[self::DATA_ENCODING] === self::ENCODE_JSON_SIMPLE) {
                $this->expiresAt[$key] = $item[self::DATA_EXPIRATION];
                $this->data[$key]      = json_decode($item[self::DATA_BODY], true);
                $this->signed[$key] = true;
            } else {
                $this->data[$key] = $string;
                $this->expiresAt[$key] = time() + self::DEFAULT_TTL;
            }
        }
        
        return $this->data;
    }
    
    /**
     * @param mixed $packed
     */
    public function setPacked($packed)
    {
        $this->packed = $packed;
        $this->unpack();
    }
    
    public function set ($key, $data, $ttl = null) 
    {
        $ttl = $ttl ?? self::DEFAULT_TTL;
        
        $this->data[$key]      = $data;
        $this->expiresAt[$key] = time() + $ttl;
        $this->touched[$key] = $key;
    }
    
    public function touch ($key)
    {
        if (isset($this->data[$key])) {
            $this->touched[$key] = $key;
            return true;
        }
        
        return false;
    }
    
    public function get ($key, $default = null) 
    {
        if (!isset($this->data[$key])) {
            return $default;
        }
        
        if ($this->expiresAt[$key] < time()) {
            $this->delete($key);
            return $default;
        }
        
        return $this->data[$key];
    }
    
    public function delete ($key) 
    {
        $this->data[$key] = null;
        $this->expiresAt[$key] = 0;
        $this->touched[$key] = $key;
    }
    
    public function pack()
    {
        $this->packed = [];
        $time = time();
        
        foreach ($this->touched as $key) {
            $value = $this->data[$key];
            
            $expireAt = isset($this->expiresAt[$key]) ? $this->expiresAt[$key] : $time + self::DEFAULT_TTL;
            if ($value === null) {
                $this->packed[$key] = null;
            } else if($this->encoder === self::ENCODE_JSON_SIMPLE) {
                $this->packed[$key] = self::ENCODE_JSON_SIMPLE
                    . self::PACK_DELIMITER
                    . $expireAt
                    . self::PACK_DELIMITER
                    . json_encode($value, JSON_UNESCAPED_UNICODE);
            } else if($this->encoder === self::ENCODE_CAT) {
                $this->packed[$key] = self::ENCODE_JSON_SIMPLE
                    . self::PACK_DELIMITER
                    . $expireAt
                    . self::PACK_DELIMITER
                    . $value;
            } else {
                $this->packed[$key] = $value;
            }
        }
        
        return $this->packed;
    }
    
    public function isSinged ($key) 
    {
        return isset($this->signed[$key]) && $this->signed[$key];
    }
    
    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * @return array
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
    
    /**
     * @return array
     */
    public function getSigned()
    {
        return $this->signed;
    }
}