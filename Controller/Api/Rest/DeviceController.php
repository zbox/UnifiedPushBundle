<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Controller\Api\Rest;

use Zbox\UnifiedPushBundle\Provider\DeviceProviderInterface;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @RouteResource("device")
 * @NamePrefix("zbox_api_device")
 */
class DeviceController extends FOSRestController
{
    /**
     * @var DeviceProviderInterface
     */
    protected $deviceProvider;

    /**
     * @var FormInterface
     */
    protected $deviceForm;

    /**
     * @param DeviceProviderInterface $deviceProvider
     * @param FormInterface $deviceForm
     */
    public function __construct(DeviceProviderInterface $deviceProvider, FormInterface $deviceForm)
    {
        $this->deviceProvider   = $deviceProvider;
        $this->deviceForm       = $deviceForm;
    }

    /**
     * REST GET item
     *
     * @ApiDoc(
     *   description="Get device by id",
     *   output = "Zbox\UnifiedPushBundle\Entity\Device",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the notification is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="device")
     *
     * @param int     $id      the notification id

     * @return Response
     */
    public function getByIdAction($id)
    {
        $device = $this->deviceProvider->getById($id);

        if (false === $device) {
            throw $this->createNotFoundException("Device does not exist.");
        }

        return new View($device);
    }

    /**
     * @ApiDoc(
     *   description="Create new device",
     *   resource = true,
     *   input = "Zbox\UnifiedPushBundle\Form\Type\DeviceType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     *
     * @return View
     */
    public function postAction(Request $request)
    {
        //$this->processForm();
        return new View(null, Codes::HTTP_NO_CONTENT);
    }
    
    /**
     * @ApiDoc(
     *   description="Delete device",
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param int     $id      the device id
     *
     * @return View
     */
    public function deleteAction($id)
    {
        $this->deviceProvider->markAsDeleted($id);

        return new View(null, Codes::HTTP_NO_CONTENT);
    }
}
