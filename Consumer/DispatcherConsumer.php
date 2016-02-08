<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Consumer;

use Zbox\UnifiedPushBundle\Provider\DeviceProviderInterface;
use Zbox\UnifiedPushBundle\Provider\NotificationProviderInterface;
use Zbox\UnifiedPush\Notification\Notification;
use Zbox\UnifiedPushBundle\Entity\Mapper\NotificationMapper;
use Zbox\UnifiedPushBundle\Exception\NotFoundException;
use Zbox\UnifiedPushBundle\Traits\LoggerTrait;

use Zbox\UnifiedPush\Dispatcher;
use Zbox\UnifiedPush\Message\RecipientDevice;

use PhpAmqpLib\Message\AMQPMessage;

class DispatcherConsumer implements ConsumerInterface
{
    use LoggerTrait;

    /**
     * @var NotificationProviderInterface
     */
    private $notificationProvider;

    /**
     * @var NotificationMapper
     */
    private $notificationMapper;

        /**
     * @var DeviceProviderInterface
     */
    private $deviceProvider;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(
        NotificationProviderInterface $notificationProvider,
        NotificationMapper $notificationMapper,
        DeviceProviderInterface $deviceProvider,
        Dispatcher $dispatcher
    ) {
        $this->notificationProvider = $notificationProvider;
        $this->notificationMapper   = $notificationMapper;
        $this->deviceProvider       = $deviceProvider;
        $this->dispatcher           = $dispatcher;
    }

    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        try {
            $body = json_decode($msg->body);

            if (empty($body) || !property_exists($body, 'notificationId')) {

                $this->log('Invalid notification');
                return true;
            }

            $notificationId = $body->notificationId;
            $notification   = $this->getNotification($notificationId);



            $this->dispatcher->sendNotification($notification);

            $responseHandler = $this->dispatcher->getResponseHandler();
            $responseHandler->handleResponseCollection();

            $this->processInvalidRecipients();

            if ($this->isDelivered()) {
                $this->notificationProvider->markDelivered($notificationId);
            } else {
                $this->notificationProvider->markFailed($notificationId);
            }

            return true;

        } catch (NotFoundException $e) {
            $this->log($e->getMessage());
            return true;

        } catch (\Exception $e) {
            fwrite(STDERR, sprintf("Exception: %s", $e->getMessage()));
            return false;
        }
    }

    /**
     * @param int $notificationId
     * @return Notification
     */
    protected function getNotification($notificationId)
    {
        $notificationEntity = $this->notificationProvider->getById($notificationId);

        if (empty($notification)) {
            throw new NotFoundException(sprintf('Notification with id %d does not exists', $notificationId));
        }

        $notification = $this->notificationMapper->mapEntityToModel($notificationEntity);

        return $notification;
    }

    protected function processInvalidRecipients()
    {
        $responseHandler = $this->dispatcher->getResponseHandler();
        $invalidRecipients = $responseHandler->getInvalidRecipients();

        /** @var RecipientDevice $recipientDevice */
        foreach ($invalidRecipients as $recipientDevice) {
            if ($recipientDevice->isInvalidRecipient()) {
                $this->deviceProvider->updateInvalidRecipients(
                    $recipientDevice->getIdentifier(),
                    $recipientDevice->getIdentifierStatus()
                );
            }
        }
    }

    /**
     * @return bool
     */
    protected function isDelivered()
    {
        $responseHandler = $this->dispatcher->getResponseHandler();

        return $responseHandler->getMessageErrors()->count() == 0;
    }
}
