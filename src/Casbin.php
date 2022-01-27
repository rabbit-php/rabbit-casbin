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

    /**
     * @param \Casbin\Log\Logger $logger
     * @param array $config
     */
    public function __construct(Logger $logger, public readonly array $config = [])
    {
        Log::setLogger($logger);
    }

    /**
     * @Author Albert 63851587@qq.com
     * @DateTime 2020-10-26
     * @return \Casbin\Enforcer
     */
    public function enforcer(): Enforcer
    {
        if ($this->enforcer === null) {
            sync('casbin', fn (): Enforcer => $this->enforcer = new Enforcer($this->model, $this->adapter));
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
