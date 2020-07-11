<?php
declare(strict_types=1);

namespace Rabbit\Casbin;

use Casbin\Enforcer;
use Casbin\Exceptions\CasbinException;
use Casbin\Log\Log;
use Casbin\Model\Model;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class Casbin
 * @package Rabbit\Casbin
 */
class Casbin
{
    /** @var Enforcer */
    private ?Enforcer $enforcer = null;
    /** @var AdapterInterface */
    private ?AdapterInterface $adapter = null;
    /** @var Model */
    private ?Model $model = null;
    /** @var array */
    private array $config;

    /**
     * @param \Casbin\Log\Logger $logger
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
     * @param bool $newInstance
     * @return Enforcer
     */
    public function enforcer($newInstance = false): Enforcer
    {
        if ($newInstance || is_null($this->enforcer)) {
            sycn(function () {
                $this->enforcer = new Enforcer($this->model, $this->adapter);
            });
        }
        return $this->enforcer;
    }

    /**
     * @param $name
     * @param $params
     * @return mixed
     */
    public function __call($name, $params)
    {
        return call_user_func_array([$this->enforcer(), $name], $params);
    }
}