<?php
namespace AppBundle\Service;




class MMailer
{


    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }


    public function mail($post, $msg) {

         $message = \Swift_Message::newInstance()
            ->setSubject('hello')
            ->setFrom('lupo@xs4all.nl')
            ->setTo("lupo@xs4all.nl")
            ->addPart($post . $msg);
        //->setBody($this->templating->render('Emails/mail.html.twig',array('post'=>$post)),'text/html');

        $this->mailer->send($message);
    }
}
