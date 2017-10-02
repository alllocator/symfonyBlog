<?php

namespace Test\AppBundle\Controller;

use AppBundle\Controller;
use AppBundle\Controller\BlogController;
//use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase

{
    function testgetPaginatedAction()
    {
        $client = static::createClient();

        $client->request(
            'GET', '/blogposts',
            array(), /* request params */
            array(), /* files */
            array('X-Requested-With' => "XMLHttpRequest"));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

