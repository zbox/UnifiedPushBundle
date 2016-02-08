<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle;

/**
 * Class ZboxUnifiedPushEvents
 * @package Zbox\UnifiedPushBundle
 */
final class ZboxUnifiedPushEvents
{
    /**
     * The NOTIFICATION_CREATE event occurs after message
     * was successfully packed to be sent to notification service (NS)
     *
     * @var string
     */
    const NOTIFICATION_CREATE    = 'notification.create';

    /**
     * The NOTIFICATION_DISPATCH event occurs after message was sent to NS
     *
     * @var string
     */
    const NOTIFICATION_DISPATCH  = 'notification.dispatch';

    /**
     * The NOTIFICATION_SUCCESS event occurs on positive response of NS
     *
     * @var string
     */
    const NOTIFICATION_SUCCESS   = 'notification.success';

    /**
     * The NOTIFICATION_ERROR event occurs on error response of NS
     *
     * @var string
     */
    const NOTIFICATION_ERROR     = 'notification.error';
}
