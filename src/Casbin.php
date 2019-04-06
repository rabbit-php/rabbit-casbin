<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/16
 * Time: 17:54
 */

namespace rabbit\casbin;

use Casbin\Enforcer;
use Casbin\Log\Log;
use Casbin\Model\Model;
use rabbit\casbin\Model\CasbinRule;

/**
 * Class Casbin
 * @package rabbit\casbin
 */
class Casbin
{
    /** @var bool */
    private $isInit = false;
    /** @var Enforcer */
    private $enforcer;
    /** @var Adapter */
    private $adapter;
    /** @var Model */
    private $model;
    /** @var array */
    private $config = [];

    /**
     * @param array $config
     */
    public function __construct(\Casbin\Log\Logger $logger, array $config = [])
    {
        $this->config = $config;
        Log::setLogger($logger);
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     *
     */
    private function init()
    {
        if (!$this->isInit) {
            $db = CasbinRule::getDb();
            $tableName = CasbinRule::tableName();
            $table = $db->getTableSchema($tableName);
            if (!$table) {
                $res = $db->createCommand()->createTable($tableName, [
                    'id' => 'pk',
                    'ptype' => 'string',
                    'v0' => 'string',
                    'v1' => 'string',
                    'v2' => 'string',
                    'v3' => 'string',
                    'v4' => 'string',
                    'v5' => 'string',
                ])->execute();
            }
        }
    }

    /**
     * @param bool $newInstance
     * @return Enforcer
     * @throws \Casbin\Exceptions\CasbinException
     */
    public function enforcer($newInstance = false): Enforcer
    {
        if ($newInstance || is_null($this->enforcer)) {
            $this->init();
            $this->enforcer = new Enforcer($this->model, $this->adapter);
        }
        return $this->enforcer;
    }

    /**
     * @param $name
     * @param $params
     * @return mixed
     * @throws \Casbin\Exceptions\CasbinException
     */
    public function __call($name, $params)
    {
        return call_user_func_array([$this->enforcer(), $name], $params);
    }
}