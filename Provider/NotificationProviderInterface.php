<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Provider;

use Zbox\UnifiedPushBundle\Entity\Notification;

/**
 * Interface NotificationProviderInterface
 * @package Zbox\UnifiedPushBundle\Provider
 */
interface NotificationProviderInterface
{
    /**
     * @param int $start
     * @param int $limit
     * @return Notification[]
     */
    public function getAll($start, $limit);

    /**
     * @param int $id
     * @return Notification
     */
    public function getById($id);

    /**
     * @param int $id
     */
    public function markDelivered($id);

    /**
     * @param int $id
     */
    public function markFailed($id);
}
