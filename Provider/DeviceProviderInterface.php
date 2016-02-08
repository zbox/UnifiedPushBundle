<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Provider;

use Zbox\UnifiedPushBundle\Entity\Device;

/**
 * Interface DeviceProviderInterface
 * @package Zbox\UnifiedPushBundle\Provider
 */
interface DeviceProviderInterface
{
    /**
     * @param int $deviceId
     * @param string $status
     */
    public function updateInvalidRecipients($deviceId, $status);

    /**
     * @param int $id
     * @return Device
     */
    public function getById($id);

    /**
     * @param int $id
     */
    public function markAsDeleted($id);
}
