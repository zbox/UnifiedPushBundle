<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Entity\Collection;

use Zbox\UnifiedPushBundle\Entity\Notification;

class NotificationCollection
{
    /**
     * @var Notification[]
     */
    public $notifications;

    /**
     * @var integer
     */
    public $offset;

    /**
     * @var integer
     */
    public $limit;

    /**
     * @param Notification[]  $notifications
     * @param integer $offset
     * @param integer $limit
     */
    public function __construct($notifications = [], $offset = null, $limit = null)
    {
        $this->notifications = $notifications;
        $this->offset = $offset;
        $this->limit = $limit;
    }
}
