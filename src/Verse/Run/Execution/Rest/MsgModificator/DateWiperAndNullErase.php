<?php

namespace Verse\Run\Execution\Rest\MsgModificator;

use Mu\Helper\DateTime;
use Verse\Run\ChannelMessage\ChannelMsg;
use Verse\Run\Execution\PluginProto;
use Verse\Run\RunRequest;

class DateWiperAndNullErase extends MsgModificatorProto
{
    const ITEMS_KEY = 'items';
    
    private $fields = [
        'created_at'     => 1,
        'updated_at'     => 1,
        'deleted_at'     => 1,
        'delivery_at'    => 1,
        'delivery_until' => 1,
        'activated_at'   => 1,
        'start_at'       => 1,
        'end_at'         => 1,
        'expired_at'     => 1,
    ];
    
    public function process(RunRequest $request, ChannelMsg $message)
    {
        if (!isset($message->body) || !is_array($message->body) || !array_key_exists('data', $message->body)) {
            return ;
        }
        
        $data = &$message->body['data'];
        
        if (is_object($data) && $data instanceof \stdClass) {
            $data = get_object_vars($data);
        }
        
        if (!is_array($data)) {
            $data = new \stdClass();
            return ;
        }
        
        if (!$data) {
            $data[self::ITEMS_KEY] = [];
            return;
        }
        
        if (!isset($data[self::ITEMS_KEY]) && isset($data[0], $data[count($data)-1])) {
            $data = [
                self::ITEMS_KEY => $data,
            ];
        }
        
        if ($request->getParamOrData('extend')) {
            foreach ($data as $subjectName => &$subjectCollection) {
                foreach ($subjectCollection as $key => &$row) {
                    $this->format($row);
                }
            }
        } else {
            if (isset($data[self::ITEMS_KEY])) {
                foreach ($data[self::ITEMS_KEY] as $key => &$row) {
                    $this->format($row);
                }
            } else {
                $this->format($data);
            }
        }
    }
    
    public function format(&$row)
    {
        foreach ($row as $key => &$value) {
            if (is_null($value)) {
                unset($row[$key]);
            } else if(isset($this->fields[$key])) {
                $value = DateTime::create($value)->getTimestamp();;
            }
        }
    }
    
}
