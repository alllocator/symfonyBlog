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

class BlogController extends FOSRestController
{
    private function mail($post) {
        $message = \Swift_Message::newInstance()
            ->setSubject('hello')
            ->setFrom('lupo@xs4all.nl')
            ->setTo("lupo@xs4all.nl")
            ->setBody($this->renderView('Emails/mail.html.twig',array('post'=>$post)),'text/html');

        $this->get('mailer')->send($message);
    }

    /**
     * @Rest\Get("/blogposts/{order}/{page}/{size}" , defaults={"order" = 1, "page"=0 , "size"=0 })
     */
    public function getPaginatedAction($order, $page, $size)
    {
        $order?$order='ASC':$order='DESC';

        $limit = null;
        $offset = null;

        if ($page == 0)
        {
            $limit = $size;
        }
        elseif ($page>0)
        {
            $offset = $page * $size;
            $limit = $size;
        }


        $restresult = $this->getDoctrine()->getRepository('AppBundle:BlogPost')->findBy
        (
            array(),
            array('dateCreated' => $order),
            $limit,
            $offset
        );
        if ($restresult === null) {
            return new View("no posts found", Response::HTTP_NOT_FOUND);
        }
        return $restresult;

    }

    /**
    * @Rest\Get("/blogpost/{id}")
    */
    public function idAction($id)
    {
        $singleresult = $this->getDoctrine()->getRepository('AppBundle:BlogPost')->find($id);
        if ($singleresult === null) {
            return new View("posts not found", Response::HTTP_NOT_FOUND);
        }
        return $singleresult;
    }

    /**
     * @Rest\Post("/blogpost")
     */
    public function postAction(Request $request)
    {
        $data = new BlogPost;
        $post = $request->get('post');

        if(empty($post))
        {
            return new View("no blog text received", Response::HTTP_NOT_ACCEPTABLE);
        }
        $data->setPost($post);

        // new blog, set initial timestamp
        $data->setDateCreated(new \DateTime("now"));

        $blogTags = $request->get('blogtags');
        if(!empty($blogTags))
        {
            $data->setBlogTags($blogTags);
        }

        $publishedStatus = $request->get('status');
        if (!empty($publishedStatus)) {
           $data->setStatus($publishedStatus);
        } else {
            $data->setStatus(false);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();

        $this->mail($post);

        return new View("Post Added Successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/blogpost/{id}")
     */
    public function updateAction($id,Request $request)
    {
        // possibly an issue with the $request doesn't hold the values
        // when using form-data in postmaster it fails
        // raw json {"post":"test 0123","status":true} and x-www-form-urlencoded are OK
        $post = $request->get('post');
        $status = $request->get('status');

        $sn = $this->getDoctrine()->getManager();
        $blogPost = $this->getDoctrine()->getRepository('AppBundle:BlogPost')->find($id);
        if (empty($blogPost)) {
            return new View("post not found", Response::HTTP_NOT_FOUND);
        }
        elseif(!empty($post) && !empty($status)){
            $blogPost->setPost($post);
            $blogPost->setStatus($status);
            $sn->flush();
            return new View("Post Updated Successfully", Response::HTTP_OK);
        }
        elseif(empty($post) && !empty($status)){
            $blogPost->setStatus($status);
            $sn->flush();
            return new View("Published Status Updated Successfully", Response::HTTP_OK);
        }
        elseif(!empty($post) && empty($status)){
            $blogPost->setPost($post);
            $sn->flush();
            return new View("Post Updated Successfully", Response::HTTP_OK);
        }
        else return new View("Either Post or Status should be changed $request .. ", Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @Rest\Delete("/blogpost/{id}")
     */
    public function deleteAction($id)
    {

        $sn = $this->getDoctrine()->getManager();
        $blogPost = $this->getDoctrine()->getRepository('AppBundle:BlogPost')->find($id);
        if (empty($blogPost)) {
            return new View("Post not found", Response::HTTP_NOT_FOUND);
        }
        else {
            $sn->remove($blogPost);
            $sn->flush();
        }
        return new View("deleted successfully", Response::HTTP_OK);
    }

}