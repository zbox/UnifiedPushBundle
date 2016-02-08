<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Traits;

use Psr\Log\LoggerInterface;

/**
 * Class LoggerTrait
 * @package Zbox\UnifiedPushBundle\Traits
 */
trait LoggerTrait
{
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function log($text)
    {
        if (isset($this->logger)) {
            $this->logger->log($text);
        }

        return $this;
    }

}
