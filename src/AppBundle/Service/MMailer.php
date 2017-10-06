<?php
namespace AppBundle\Service;

class MMailer
{
    private $mailer;
    private $adminEmail;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, $templating, $adminEmail)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->adminEmail = $adminEmail;
    }


    public function mail($post, $msg) {

         $message = \Swift_Message::newInstance()
            ->setSubject('hello')
            ->setFrom($this->adminEmail)
            ->setTo($this->adminEmail)
            ->setBody($this->templating->render('Emails/mail.html.twig',array('post'=>$post, 'actionMsg' => $post)),'text/html');

        $this->mailer->send($message);
    }
}
