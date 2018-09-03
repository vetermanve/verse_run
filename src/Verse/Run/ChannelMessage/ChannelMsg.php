<?php


namespace Run\ChannelMessage;


use Run\Util\ChannelState;

class ChannelMsg
{
    /**
     * Айди сообщения.
     *
     * Если сообщение исходящее, то это айди исходящего сообщения
     * Если сообщение ответ на входящее - айди входящего на которое ответ.
     *
     * @var
     */
    protected $uid;
    
    /**
     * Метаданные ответа, могу использоваться, не исользоваться
     * или трактоваться по разному в зависимости от того в какой
     * канал отправляется сообщение
     *
     * @var array
     */
    protected $meta = [];
    
    /**
     * Объек состояния канала
     * Оснавная его фишка в том, что записи в состоянии
     * имет срок жизни, и не данные могу быть прочитаны из
     * адаптера если они уже не валидны, даже если они пришли
     *
     * @var ChannelState
     */
    protected $channelState;
    
    /**
     * Страка обознанения места назначения пакета
     * Может трактоваться по разному в зависимости от
     * канала отправки.
     *
     * @var string
     */
    protected $destination = '';
    
    /**
     * Тело сообщения, может быть массивом
     * объектом или строкой.
     *
     * @var string|array|\stdClass
     */
    public $body;
    
    /**
     * Код ответа;
     *
     * @var int;
     */
    public $code = 0;
    
    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }
    
    /**
     * @param mixed $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }
    
    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
    
    /**
     * @return array
     */
    public function getAllMeta()
    {
        return $this->meta;
    }
    
    /**
     * @param $metaKey
     * @param $metaVal
     */
    public function setMeta($metaKey, $metaVal)
    {
        $this->meta[$metaKey] = $metaVal;
    }
    
    /**
     * @param      $metaKey
     *
     * @param null $default
     *
     * @return mixed
     */
    public function getMeta($metaKey, $default = null)
    {
        return isset($this->meta[$metaKey]) ? $this->meta[$metaKey] : $default;
    }
    
    /**
     * @param ChannelState $channelState
     */
    public function setChannelState($channelState)
    {
        $this->channelState = $channelState;
    }
    
    /**
     * Вернуть исходящее состояние канала
     *
     * @return ChannelState
     */
    public function getChannelState()
    {
        return $this->channelState;
    }
    
    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }
    
    /**
     * @param mixed $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }
    
    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }
    
    public function getDebugInfo()
    {
        return [
            'code'             => $this->code,
            'destination'      => $this->destination,
            'channel_data'     => $this->channelState ? $this->channelState->getData() : null,
            'channel_signed'   => $this->channelState ? $this->channelState->getSigned() : null,
            'body_type'        => gettype($this->body),
        ];
    }
}