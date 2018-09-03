<?php


namespace Verse\Run\Module;


use Verse\Run\RunContext;
use Verse\Run\RunModuleProto;

class RpcServices extends RunModuleProto
{
    const F_SERVICE    = 'service';
    const F_CLASS      = 'class';
    const F_CLOUD = 'cloud_type';
    const F_CLOUD_HOST = 'cloud_host';
    const F_QUEUE      = 'queue';
    const F_TRANSPORT  = 'transport';
    const F_HOST       = 'host';
    const F_PORT       = 'port';
    const F_PROTO_CLOUD = 'proto_cloud';
    
    const F_CODE_BASE = 'parent_service';
    
    const CLOUD_AMQP_MAIN = 'amqp';
    const CLOUD_AMQP_FILE = 'amqp_file';
    const CLOUD_MSG_PACK_TRANSACTION = 'msg_pack';
    
    const CLASS_PREFIX = '\\iConto\\Service\\';
    
    /**
     * Конфиги сервиса
     *
     * @var []
     */
    private $servicesConfig;
    
    /**
     * Конфиги облаков
     *
     * @var []
     */
    private $cloudsConfig;
    
    public function getServiceConfig($serviceName)
    {
        if (!$this->servicesConfig) {
            $this->_loadServicesConfig();
        }
        
        if (!$this->cloudsConfig) {
            $this->_loadCloudsConfig();
        }
        
        if (!isset($this->servicesConfig[$serviceName])) {
            trigger_error('Service config "' . $serviceName . '" not found', E_USER_WARNING);
            
            return null;
        }
        
        $mainConfig = $this->servicesConfig[$serviceName];
        
        $cloud = isset($mainConfig[self::F_CLOUD]) ? $mainConfig[self::F_CLOUD] : self::CLOUD_AMQP_MAIN;
        
        if (!isset($this->cloudsConfig[$cloud])) {
            trigger_error('Cloud type "' . $cloud . '" not found in configs', E_USER_WARNING);
            
            return null;
        }
        
        $mainConfig[self::F_CLASS] = self::CLASS_PREFIX . $mainConfig[self::F_CLASS];
        $mainConfig[self::F_QUEUE] = 'rpc.service.' . strtolower($mainConfig[self::F_CODE_BASE]);
        
        $cloudConfig = $this->cloudsConfig[$cloud];
        
        return $mainConfig + $cloudConfig;
    }
    
    private function _loadCloudsConfig()
    {
        $externalConfig = $this->context->getLink(RunContext::GLOBAL_CONFIG, []);
        
        $this->cloudsConfig = [
            self::CLOUD_AMQP_MAIN            => [
                self::F_TRANSPORT => \Mu\Transport::TRANSPORT_TYPE_AMQP_ASYNC,
                self::F_HOST      => 'localhost',
                self::F_PORT      => 5672,
            ],
            self::CLOUD_AMQP_FILE            => [
                self::F_PROTO_CLOUD => self::CLOUD_AMQP_MAIN,
            ],
            self::CLOUD_MSG_PACK_TRANSACTION => [
                self::F_TRANSPORT => \Mu\Transport::TRANSPORT_TYPE_MSGPACK,
                self::F_HOST      => 'localhost',
                self::F_PORT      => 1988,
            ]
        ];
        
        foreach ($this->cloudsConfig as $cloudId => &$cloudConfig) {
            if (isset($cloudConfig[self::F_PROTO_CLOUD])) {
                $cloudConfig = $this->cloudsConfig[$cloudConfig[self::F_PROTO_CLOUD]] + $cloudConfig;
            }
            
            if (isset($externalConfig[$cloudId]) && is_array($externalConfig[$cloudId])) {
                $cloudConfig = $externalConfig[$cloudId] + $cloudConfig;
            }
        }
        
        unset($cloudConfig);
    }
    
    private function _loadServicesConfig()
    {
        $this->servicesConfig = [
            'User'           => [
                self::F_SERVICE   => 'User',
                self::F_CODE_BASE => 'User',
                self::F_CLASS     => 'User\UserService',
            ],
            'Transaction'    => [
                self::F_SERVICE   => 'Transaction',
                self::F_CODE_BASE => 'Transaction',
                self::F_CLASS     => 'Transaction\TransactionService',
                self::F_CLOUD     => self::CLOUD_MSG_PACK_TRANSACTION,
            ],
            'Auth'           => [
                self::F_SERVICE   => 'Auth',
                self::F_CODE_BASE => 'Auth',
                self::F_CLASS     => 'Auth\AuthService',
            ],
            'TransactionNew' => [
                self::F_SERVICE   => 'TransactionNew',
                self::F_CODE_BASE => 'Transaction',
                self::F_CLASS     => 'Transaction\TransactionNewService',
            ],
            'RoleAccess'     => [
                self::F_SERVICE   => 'RoleAccess',
                self::F_CODE_BASE => 'Auth',
                self::F_CLASS     => 'Auth\RoleAccessService',
            ],
            'Notification'   => [
                self::F_SERVICE   => 'Notification',
                self::F_CODE_BASE => 'Notification',
                self::F_CLASS     => 'Notification\NotificationService',
            ],
            'CompanyClient'  => [
                self::F_SERVICE   => 'CompanyClient',
                self::F_CODE_BASE => 'Company',
                self::F_CLASS     => 'Company\CompanyClientService',
            ],
            'Company'        => [
                self::F_SERVICE   => 'Company',
                self::F_CODE_BASE => 'Company',
                self::F_CLASS     => 'Company\CompanyService',
            ],
            'Deposit'        => [
                self::F_SERVICE   => 'Deposit',
                self::F_CODE_BASE => 'Company',
                self::F_CLASS     => 'Company\DepositService',
            ],
            'File'           => [
                self::F_SERVICE   => 'File',
                self::F_CODE_BASE => 'File',
                self::F_CLASS     => 'File\FileService',
                self::F_CLOUD     => self::CLOUD_AMQP_FILE,
            ],
            'Geolocation'    => [
                self::F_SERVICE   => 'Geolocation',
                self::F_CLASS     => 'Geolocation\GeolocationService',
                self::F_CODE_BASE => 'Geolocation'
            ],
            'Payment'        => [
                self::F_SERVICE   => 'Payment',
                self::F_CODE_BASE => 'Payment',
                self::F_CLASS     => 'Payment\PaymentService',
            ],
            'Card'           => [
                self::F_SERVICE   => 'Card',
                self::F_CODE_BASE => 'Payment',
                self::F_CLASS     => 'Payment\CardService',
            ],
            'Bank'           => [
                self::F_SERVICE   => 'Bank',
                self::F_CODE_BASE => 'Payment',
                self::F_CLASS     => 'Payment\BankService',
            ],
            'Template'       => [
                self::F_SERVICE   => 'Template',
                self::F_CODE_BASE => 'Template',
                self::F_CLASS     => 'Template\TemplateService',
            ],
            'UserBalance'    => [
                self::F_SERVICE   => 'UserBalance',
                self::F_CODE_BASE => 'Payment',
                self::F_CLASS     => 'Payment\UserBalanceService',
            ],
            'Chat'           => [
                self::F_SERVICE   => 'Chat',
                self::F_CODE_BASE => 'Notification',
                self::F_CLASS     => 'Chat\ChatService',
            ],
            'Feed'           => [
                self::F_SERVICE   => 'Feed',
                self::F_CLASS     => 'Feed\FeedService',
                self::F_CODE_BASE => 'Feed',
            ],
            'Event'          => [
                self::F_SERVICE   => 'Event',
                self::F_CLASS     => 'Event\EventService',
                self::F_CODE_BASE => 'Rest',
            ],
            'ShortUrl' => [
                self::F_SERVICE   => 'ShortUrl',
                self::F_CLASS     => 'ShortUrl\ShortUrlService',
                self::F_CODE_BASE => 'Rest',
            ],
        ];
    }
}