<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Entity\Repository;

use Zbox\UnifiedPushBundle\Entity\Device;

use Doctrine\ORM\EntityRepository;

/**
 * Class DeviceRepository
 * @package Zbox\UnifiedPushBundle\Entity\Repository
 */
class DeviceRepository extends EntityRepository
{
    /**
     * Find devices by ids
     *
     * @param array $deviceIds
     * @return Device[]
     */
    public function findDevices(array $deviceIds)
    {
        $qb = $this->createQueryBuilder('device');

        return $qb->where($qb->expr()->in('device.id', $deviceIds))->getQuery()->execute();
    }

    public function updateDeviceStatus($deviceId, $status)
    {

    }
}
