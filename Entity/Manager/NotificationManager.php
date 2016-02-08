<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;

use Zbox\UnifiedPushBundle\Provider\NotificationProviderInterface;
use Zbox\UnifiedPushBundle\Entity\Notification;
use Zbox\UnifiedPushBundle\Entity\Repository\NotificationRepository;

/**
 * Class NotificationManager
 * @package Zbox\UnifiedPushBundle\Entity\Manager
 */
class NotificationManager implements NotificationProviderInterface
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
     * @param int $start
     * @param int $limit
     * @return Notification[]
     */
    public function getAll($start, $limit)
    {
        return $this->getNotificationRepository()->findAllNotifications($start, $limit);
    }

    /**
     * @param int $id
     * @return Notification
     */
    public function getById($id)
    {
        return $this->getNotificationRepository()->find($id);
    }

    /**
     * @param int $id
     */
    public function markDelivered($id)
    {
        $this->saveState($id, Notification::STATE_DELIVERED);
    }

    /**
     * @param int $id
     */
    public function markFailed($id)
    {
        $this->saveState($id, Notification::STATE_FAILED);
    }

    /**
     * @param int $id
     * @param string $state
     */
    protected function saveState($id, $state)
    {
        $notification = $this->getById($id);
        $notification->setState($state);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    /**
     * @return NotificationRepository
     */
    protected function getNotificationRepository()
    {
        return $this->entityManager->getRepository('ZboxUnifiedPushBundle:Notification');
    }
}
