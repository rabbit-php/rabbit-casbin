<?php

declare(strict_types=1);

namespace Rabbit\Casbin;

use Casbin\Log\Log;
use Casbin\Enforcer;
use Casbin\Log\Logger;
use Casbin\Model\Model;
use Casbin\Persist\Adapter;

/**
 * Class Casbin
 * @package Rabbit\Casbin
 */
class Casbin
{
    private ?Enforcer $enforcer = null;
    private ?Adapter $adapter = null;
    private ?Model $model = null;
    private array $config;

    /**
     * @param \Casbin\Log\Logger $logger
     * @param array $config
     */
    public function __construct(Logger $logger, array $config = [])
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
            sync(fn () => $this->enforcer = new Enforcer($this->model, $this->adapter));
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
