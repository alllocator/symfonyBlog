<?php
/**
 * Created by PhpStorm.
 * User: lp
 * Date: 9/30/2017
 * Time: 4:35 PM
 */
namespace AppBundle\Util\Mailer;

class mailer{

    var $mailer;

    function __construct() {
        //$this->mailer = new \Swift_Mailer(\Swift_Transport::);
    }

    public function sendIt() {

        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('lupo@xs4all.nl')
            ->setTo('lupo@xs4all.nl')
            ->setBody(
                $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'Emails/mail.html.twig'
                ),
                'text/html'
            )
            /*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'Emails/registration.txt.twig',
                    array('name' => $name)
                ),
                'text/plain'
            )
            */
        ;

        $this->mailer->send($message);

    }
}
