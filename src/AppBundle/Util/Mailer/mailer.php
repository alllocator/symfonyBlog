<?php
/**
 * Created by PhpStorm.
 * User: lp
 * Date: 9/30/2017
 * Time: 4:35 PM
 */
namespace AppBundle\Util\Mailer;

class mailer{

    //toDo use this one instead of the mailer in the blogController

    private function mail($post) {
        $message = \Swift_Message::newInstance()
            ->setSubject('hello')
            ->setFrom('lupo@xs4all.nl')
            ->setTo("lupo@xs4all.nl")
            ->setBody($this->renderView('Emails/mail.html.twig',array('post'=>$post)),'text/html');

        $this->get('mailer')->send($message);
    }


}
