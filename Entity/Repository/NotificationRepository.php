<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Entity\Repository;

use Zbox\UnifiedPushBundle\Entity\Notification;

use Doctrine\ORM\EntityRepository;

/**
 * Class NotificationRepository
 * @package Zbox\UnifiedPushBundle\Entity\Repository
 */
class NotificationRepository extends EntityRepository
{
    /**
     * Find notifications that are not sent yet
     *
     * @return Notification[]
     */
    public function findNotificationsToSend()
    {
        return
            $this
                ->createQueryBuilder('notification')
                    ->where('notification.state = :state')
                    ->setParameter('state', Notification::STATE_READY)
                    ->getQuery()
                    ->execute()
                ;
    }

    /**
     * Find notifications of type and state
     *
     * @param integer $notificationType
     * @param string $state
     * @return Notification[]
     */
    public function findNotificationsOfTypeByState($notificationType, $state)
    {
        return
            $this
                ->createQueryBuilder('notification')
                    ->where('notification.notificationType = :type')
                    ->andWhere('notification.state = :state')
                    ->setParameter('type', $notificationType)
                    ->setParameter('state', $state)
                    ->getQuery()
                    ->execute()
                ;
    }

    /**
     * Find notifications by ids
     *
     * @param array $notificationIds
     * @return Notification[]
     */
    public function findNotifications(array $notificationIds)
    {
        $qb = $this->createQueryBuilder('notification');

        $expr = $qb->expr()->in('notification.id', $notificationIds);

        return $qb->where($expr)->getQuery()->execute();
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return Notification[]
     */
    public function findAllNotifications($offset, $limit)
    {
        $qb = $this->createQueryBuilder('notification');

        $qb
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->execute();

    }
}
