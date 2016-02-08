<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\Tests\Controller\Api\Rest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeviceControllerTest extends WebTestCase
{
    public function testGetById()
    {
        $client = $this->createClient();
        $client->request('GET', '/api/rest/v1/device');

        $response = $client->getResponse();

        $this->assertJsonResponse($response);
        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
        $this->assertEquals('{"devices":[]}', $response->getContent());
    }
}
