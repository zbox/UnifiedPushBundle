<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Entity\Manager;

use Zbox\UnifiedPushBundle\Provider\DeviceProviderInterface;
use Zbox\UnifiedPushBundle\Entity\Device;
use Zbox\UnifiedPushBundle\Entity\Repository\DeviceRepository;

use Doctrine\ORM\EntityManager;

/**
 * Class DeviceManager
 * @package Zbox\UnifiedPushBundle\Entity\Manager
 */
class DeviceManager implements DeviceProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $deviceId
     * @param string $status
     */
    public function updateInvalidRecipients($deviceId, $status)
    {
        $this->getDeviceRepository()->updateDeviceStatus($deviceId, $status);
    }

    /**
     * @param int $id
     * @return Device
     */
    public function getById($id)
    {
        return $this->getDeviceRepository()->find($id);
    }

    /**
     * @param int $id
     */
    public function markAsDeleted($id)
    {
        $device = $this->getById($id);
        $device->setIsDeleted(true);

        $this->entityManager->persist($device);
        $this->entityManager->flush();
    }

    /**
     * @return DeviceRepository
     */
    protected function getDeviceRepository()
    {
        return $this->entityManager->getRepository('ZboxUnifiedPushBundle:Device');
    }
}
