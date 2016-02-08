<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Entity\Mapper;

use Zbox\UnifiedPush\Message\RecipientDevice;
use Zbox\UnifiedPushBundle\Entity\Device as DeviceEntity;
use Zbox\UnifiedPushBundle\Entity\Notification;
use Zbox\UnifiedPush\Message\Type\APNS as APNSMessage;
use Zbox\UnifiedPush\Message\Type\GCM as GCMMessage;
use Zbox\UnifiedPush\Message\Type\MPNSBase as MPNSMessage;

class DeviceMapper
{
    /**
     * @param DeviceEntity $deviceEntity
     * @param int $notificationType
     * @return RecipientDevice
     */
    public function mapEntityToModel(DeviceEntity $deviceEntity, $notificationType)
    {
        $message = $this->getMessageByType($notificationType);
        $device = new RecipientDevice($deviceEntity->getIdentifier(), $message);
        $device->setIdentifierStatus($deviceEntity->getStatus());

        return $device;
    }

    /**
     * @param int $type
     * @return \Zbox\UnifiedPush\Message\MessageInterface
     */
    protected function getMessageByType($type)
    {
        $messageTypeMap = [
            Notification::TYPE_APNS   => new APNSMessage(),
            Notification::TYPE_GCM    => new GCMMessage(),
            Notification::TYPE_MPNS   => new MPNSMessage(),
        ];

        return $messageTypeMap[$type];
    }
}
