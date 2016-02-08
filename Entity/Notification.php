<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *      name="notification",
 *      indexes={@ORM\Index(name="IDX_notification_id", columns={"id"})}
 * )
 * @ORM\Entity(repositoryClass="Zbox\UnifiedPushBundle\Entity\Repository\NotificationRepository")
 * 
 * @ORM\HasLifecycleCallbacks
 */
class Notification
{
    const STATE_DRAFT      = 'draft';
    const STATE_READY      = 'ready';
    const STATE_DELIVERED  = 'delivered';
    const STATE_FAILED     = 'failed';

    const TYPE_APNS = 1;
    const TYPE_GCM  = 2;
    const TYPE_MPNS = 3;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="smallint", nullable=false)
     * @Assert\Choice(callback = "getNotificationTypes")
     */
    protected $type;

    /**
     * @var string $payload
     *
     * @ORM\Column(name="payload", type="text", nullable=false)
     */
    protected $payload;

    /**
     * @var string $customNotification
     *
     * @ORM\Column(name="custom_notification", type="text", nullable=true)
     */
    protected $customNotification;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Zbox\UnifiedPushBundle\Entity\Device", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="notification_device",
     *   joinColumns={
     *     @ORM\JoinColumn(name="notification_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="device_id", referencedColumnName="id")
     *   }
     * )
     */
    protected $devices;

    /**
     * @var \Datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \Datetime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @var string $state
     *
     * @ORM\Column(name="state", type="string", length=14, nullable=false)
     * @Assert\Choice(callback = "getAvailableStates")
     */
    protected $state;


    public function __construct()
    {
        $this->devices = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param string $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getCustomNotification()
    {
        return $this->customNotification;
    }

    /**
     * @param string $customNotification
     */
    public function setCustomNotification($customNotification)
    {
        $this->customNotification = $customNotification;
    }

    /**
     * @return Collection
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * @return Notification
     */
    public function resetDevices()
    {
        $this->getDevices()->clear();

        return $this;
    }

    /**
     * @param Device $device
     * @return Notification
     */
    public function addDevice(Device $device)
    {
        if (!$this->getDevices()->contains($device)) {
            $this->getDevices()->add($device);
        }

        return $this;
    }

    /**
     * @param Device $device
     * @return boolean
     */
    public function removeDevice(Device $device)
    {
        return $this->getDevices()->removeElement($device);
    }

    /**
     * @param Device $device
     * @return boolean
     */
    public function hasDevice(Device $device)
    {
        return $this->getDevices()->contains($device);
    }

    /**
     * @return \Datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \Datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \Datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \Datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return array
     */
    public static function getAvailableStates()
    {
        return [
            self::STATE_DRAFT,
            self::STATE_READY,
            self::STATE_DELIVERED,
            self::STATE_FAILED
        ];
    }

    /**
     * @return array
     */
    public static function getNotificationTypes()
    {
        return [
            self::TYPE_APNS,
            self::TYPE_GCM,
            self::TYPE_MPNS
        ];
    }
}
