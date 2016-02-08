<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Controller\Api\Rest;

use Zbox\UnifiedPushBundle\Provider\NotificationProviderInterface;
use Zbox\UnifiedPushBundle\Entity\Collection\NotificationCollection;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @RouteResource("notification")
 * @NamePrefix("zbox_api_notification")
 */
class NotificationController extends FOSRestController
{
    /**
     * @var NotificationProviderInterface
     */
    protected $notificationProvider;

    /**
     * @param NotificationProviderInterface $notificationProvider
     */
    public function __construct(NotificationProviderInterface $notificationProvider)
    {
        $this->notificationProvider = $notificationProvider;
    }

    /**
     * REST GET list
     *
     * @QueryParam(
     *      name="offset",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Starting from"
     * )
     * @QueryParam(
     *      name="limit",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Number of items"
     * )
     * @ApiDoc(
     *      description="Get all notifications",
     *      resource=true
     * )
     *
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return Response
     */
    public function getAllAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $start  = null == $offset ? 0 : $offset + 1;
        $limit  = $paramFetcher->get('limit');

        $notifications = $this->notificationProvider->getAll($start, $limit);

        return new NotificationCollection($notifications, $offset, $limit);
    }

    /**
     * REST GET item
     *
     * @ApiDoc(
     *   description="Get notification by id",
     *   output = "Zbox\UnifiedPushBundle\Entity\Notification",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the notification is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="notification")
     *
     * @param int     $id      the notification id

     * @return Response
     */
    public function getByIdAction($id)
    {
        $notification = $this->notificationProvider->getById($id);

        if (false === $notification) {
            throw $this->createNotFoundException("Notification does not exist.");
        }

        return new View($notification);
    }
}
