<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Entity\Mapper;

use Zbox\UnifiedPush\NotificationService\NotificationServices;
use Zbox\UnifiedPushBundle\Entity\Notification as NotificationEntity;
use Zbox\UnifiedPush\Notification\Notification;

class NotificationMapper
{
    /**
     * @var DeviceMapper
     */
    protected $deviceMapper;

    /**
     * @param DeviceMapper $deviceMapper
     */
    public function __construct(DeviceMapper $deviceMapper)
    {
        $this->deviceMapper = $deviceMapper;
    }

    /**
     * @param NotificationEntity $notificationEntity
     * @return Notification
     */
    public function mapEntityToModel(NotificationEntity $notificationEntity)
    {
        $deviceCollection   = $this->mapNotificationDevices($notificationEntity);
        $customNotification = $this->obtainCustomNotification($notificationEntity);
        $notificationType   = $this->getNotificationType($notificationEntity->getType());

        return
            (new Notification())
                ->setRecipients($deviceCollection)
                ->setPayload($notificationEntity->getPayload())
                ->setCustomNotificationData($customNotification)
                ->setType($notificationType)
            ;
    }

    /**
     * @param NotificationEntity $notificationEntity
     * @return array
     */
    protected function obtainCustomNotification(NotificationEntity $notificationEntity)
    {
        $customNotification = [];

        $customNotificationRaw = $notificationEntity->getCustomNotification();
        if (!empty($customNotificationRaw)) {
            $customNotification = json_decode($customNotification, true);
        }

        return $customNotification;
    }

    /**
     * @param NotificationEntity $notificationEntity
     * @return \ArrayIterator
     */
    protected function mapNotificationDevices(NotificationEntity $notificationEntity)
    {
        $devices = new \ArrayIterator();

        foreach ($notificationEntity->getDevices() as $device) {
            $devices->append(
                $this->deviceMapper->mapEntityToModel(
                    $device,
                    $notificationEntity->getType()
                )
            );
        }

        return $devices;
    }

    /**
     * @param int $type
     * @return string
     */
    protected function getNotificationType($type)
    {
        $map = [
            NotificationEntity::TYPE_APNS   => NotificationServices::APPLE_PUSH_NOTIFICATIONS_SERVICE,
            NotificationEntity::TYPE_GCM    => NotificationServices::GOOGLE_CLOUD_MESSAGING,
            NotificationEntity::TYPE_MPNS   => NotificationServices::MICROSOFT_PUSH_NOTIFICATIONS_SERVICE,
        ];

        return $map[$type];
    }
}
