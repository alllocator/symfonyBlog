<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\BlogPost;
//use AppBundle\Util\Mailer\mailer;

class HelloWorldController extends FOSRestController
{

    /**
     * @param $amount : how many lines should I count
     *
     * @param $helloCount : prints $hello on all multipliers of $helloCount
     * @param $worldCount : prints $world on all mulitipliers of $worldCount
     *          Additionally print HELLO WORLD on all multiplipiers of BOTH $helloCount AND $worldCount
     *
     * E.g. 20,3,5 => 1,2,HELLO,4,WORLD,6,7,8,HELLO,WORLD,11,HELLO,13,14,HELLO WORLD,16,17,HELLO,19,WORLD
     * @param $hello : prints $hello on all multipliers of $helloCount
     * @param $world : prints $world on all mulitipliers of $worldCount
     *
     * * @Rest\Get("/sayhello/{amount}/{helloCount}/{worldCount}/{hello}/{world}" , defaults={"amount"=100, "helloCount"=3, "worldCount"=5, "hello"="HELLO","world"="WORLD"})
     */
    public function sayHelloAction($amount, $helloCount, $worldCount, $hello, $world)
    {
        $result = [];
        for ($x = 1; $x <= $amount; $x++) {
            $helloRemainder = $x % $helloCount;
            $worldRemainder = $x % $worldCount;

            if ($helloRemainder == 0 && $worldRemainder == 0) {
                $result[] = "$hello $world";
            } elseif ($helloRemainder == 0) {
                $result[] = "$hello";
            } elseif ($worldRemainder == 0) {
                $result[] = "$world";
            } else {
                $result[] = $x;
            }
        }

        return new View($result, Response::HTTP_OK);
    }

}