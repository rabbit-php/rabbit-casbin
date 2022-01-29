<?php
declare(strict_types=1);

namespace Rabbit\Casbin;

use Casbin\Log\Logger as LoggerContract;
use Psr\Log\LoggerInterface;

/**
 * Class Logger
 * @package rabbit\casbin
 */
class Logger implements LoggerContract
{
    public bool $enable = false;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? service('logger');
    }

    /**
     * controls whether print the message.
     *
     * @param bool $enable
     */
    public function enableLog(bool $enable): void
    {
        $this->enable = $enable;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enable;
    }

    /**
     * @param mixed ...$v
     */
    public function write(...$v): void
    {
        if (!$this->enable) {
            return;
        }
        $content = '';
        foreach ($v as $value) {
            if (\is_array($value) || \is_object($value)) {
                $value = json_encode($value);
            }
            $content .= $value;
        }
        $this->logger->info($content);
    }

    /**
     * @param string $format
     * @param mixed ...$v
     */
    public function writef(string $format, ...$v): void
    {
        if (!$this->enable) {
            return;
        }
        $this->logger->info(sprintf($format, ...$v));
    }

}