<?php

namespace Test\AppBundle\Controller;

use AppBundle\Controller;
use PHPUnit\Framework\TestCase;

class BlogControllerTest
{
    function getPaginatedActionTest($order, $page, $size)
    {
        $blogController = new BlogController();
        $this->assertEquals(42, 42);
    }
}

