<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tags;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\BlogPost;
use AppBundle\Service\MMailer;

class BlogController extends FOSRestController
{
    public function mail($post, $actionMsg)
    {
        $mailer = $this->get(MMailer::class);
        $mailer->mail($post, $actionMsg);
    }

    private function getOrderedBy($order)
    {
        $order?$order='ASC':$order='DESC';
        $order = ['dateCreated' => $order];
        return $order;
    }

    private function getStateFilter($state)
    {
        if ($state == 0)
        {
            // published and non published
            $filter = [];
        }
        elseif ($state == 1)
        {
            // only published
            $filter = ['status' => 1];
        }
        elseif ($state == -1)
        {
            // only non published
            $filter = ['status' => 0];
        }
        return $filter;
    }

    private function getPagination($size, $page)
    {
        $limit = null;
        $offset = null;
        if ($size == 0)
        {
            // no size implies no pagination
        }
        elseif ($page == 0)
        {
            $limit = $size;
        }
        elseif ($page > 0)
        {
            $offset = $page * $size;
            $limit = $size;
        }

        $res['limit']  = $limit;
        $res['offset'] = $offset;

        return $res;
    }

    /**
     *
     * Get blogposts (with all possible options in one controller)
     * order: [1|0]       -> [newest first|oldest first]
     * state  [-1|0|1]    -> [unpublished|All|published]
     * page   [0,1.2....] -> pagenumber (starts at page 0) ((page)size is required when using this option)
     * size   [1,2,3...]  -> number of articles per page
     *
     * @Rest\Get("/blogposts/{order}/{state}/{page}/{size}" , defaults={"order" = 1, "state"=0, "page"=0 , "size"=0} )
     */
    public function getPaginatedAction($order, $state, $page, $size)
    {
        $order = $this->getOrderedBy($order);
        $filter = $this->getStateFilter($state);

        $paginator = $this->getPagination($size, $page);
        $limit  = $paginator['limit'];
        $offset = $paginator['offset'];

        $restresult = $this->getDoctrine()->getRepository('AppBundle:BlogPost')->findBy
        (
            $filter,
            $order,
            $limit,
            $offset
        );

        if ($restresult === null)
        {
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

        $publishedStatus = $request->get('status');
        if (!empty($publishedStatus)) {
           $data->setStatus($publishedStatus);
        } else {
            $data->setStatus(false);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();

        $this->mail($post, "POST blogpost");

        return new View("Post Added Successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/blogpost/{id}")
     */
    public function updateAction($id, Request $request)
    {
        // possibly an issue with the $request doesn't hold the values
        // when using form-data in postmaster it fails
        // raw json {"post":"test 0123","status":true} and x-www-form-urlencoded are OK
        $post = $request->get('post');
        $status = $request->get('status');

        $sn = $this->getDoctrine()->getManager();
        $blogPost = $this->getDoctrine()->getRepository('AppBundle:BlogPost')->find($id);

        if (empty($blogPost)) {
            $msg = "post not found";
            $res = Response::HTTP_NOT_FOUND;
        }
        elseif(!empty($post) && !empty($status))
        {
            $blogPost->setPost($post);
            $blogPost->setStatus($status);
            $sn->flush();
            $msg = "Post Updated Successfully";
            $res = Response::HTTP_OK;
        }
        elseif(empty($post) && !empty($status))
        {
            $blogPost->setStatus($status);
            $sn->flush();
            $msg = "Published Status Updated Successfully";
            $res = Response::HTTP_OK;
        }
        elseif(!empty($post) && empty($status))
        {
            $blogPost->setPost($post);
            $sn->flush();
            $msg = "Post Updated Successfully";
            $res = Response::HTTP_OK;
        }
        else return new View("Either Post or Status should be changed $request .. ", Response::HTTP_NOT_ACCEPTABLE);

        if ($res==Response::HTTP_OK)
        {
            $this->mail($post, "PUT blogpost: $id");
        }

        return new View($msg, $res);
    }

    /**
     * @Rest\Put("/addtag/{id}/{newTag}")
     */
    public function addTagAction($id,$newTag)
    {
        $sn = $this->getDoctrine()->getManager();
        $blogPost = $this->getDoctrine()->getRepository('AppBundle:BlogPost')->find($id);

        $tag = new Tags();
        $tag->setTag($newTag);
        $sn->persist($tag);

        $tag->addBlogPost($blogPost);
        $blogPost->addTag($tag);

        $sn->flush();
    }

    private function searchBlogpostsOnTags($seperateTags , $filter = [], $order = null, $limit = null, $offset = null) {

        $seperateTags = ['tag' => $seperateTags];


        $results = $this->getDoctrine()
            ->getRepository('AppBundle:Tags')
            ->findby(
                $seperateTags
            )
        ;
        // $number = $em->find('MyProject\Domain\Phonenumber', 1234);
        // $user = $em->getRepository('MyProject\Domain\User')->findOneBy(array('phone' => $number->getId()));
        foreach ($results as $tag)
        {
            $posts[] = $tag->getBlogposts()[0]->getId();
        }



        $results = $this->getDoctrine()
            ->getRepository('AppBundle:BlogPost')
            ->findby(
                ['id' => $posts],
                $order,
                $limit,
                $offset
            )
        ;

        return $results;

    }

    private function searchOnTagsOk($seperateTags) {

        $filter = ['tag' => $seperateTags];

        $results = $this->getDoctrine()
            ->getRepository('AppBundle:Tags')
            ->findby(
                $filter
            )
        ;
        $restResult = [];
        foreach ($results as $result)
        {
            $id   = $result->getBlogposts()[0]->getId();
            $post = $result->getBlogposts()[0]->getPost();
            // all matching tags for this post
            $tag  = $result->getTag();
            // all tags for this post
            $allTags = $result->getBlogposts()[0]->getTags();
            $a = [];
            foreach ($allTags as $aTag) {
                $a[$aTag->getId()] = $aTag->getTag();
            }
            $restResult[$id]['id'] = $id;
            $restResult[$id]['post'] = $post;
            $restResult[$id]['tags'][] = $tag;
            $restResult[$id]['alltags'] = $a;
        }
        return $restResult;
    }

    private function searchOnTags($seperateTags, $filter = [], $order = null, $limit = null, $offset = null) {

        $full_filter = ['tag' => $seperateTags];
        $full_filter = array_merge($full_filter, $filter);

        $results = $this->getDoctrine()
            ->getRepository('AppBundle:Tags')
            ->findby(
                $full_filter,
                $order,
                $limit,
                $offset
            )
        ;

        $restResult = [];
        foreach ($results as $result)
        {
            $id   = $result->getBlogposts()[0]->getId();
            $post = $result->getBlogposts()[0]->getPost();

            // all matching tags for this post
            $tag  = $result->getTag();

            // all tags for this post
            $allTags = $result->getBlogposts()[0]->getTags();
            $a = [];
            foreach ($allTags as $aTag) {
               $a[$aTag->getId()] = $aTag->getTag();
            }

            $restResult[$id]['id'] = $id;
            $restResult[$id]['post'] = $post;
            $restResult[$id]['tags'][] = $tag;
            $restResult[$id]['alltags'] = $a;
        }

        return $restResult;
    }

    /**
     * @Rest\Get("/searchTagged/{tags}",
     *          requirements={"tags": "[a-zA-Z0-9\/]+"}
     *     )
     *  E.g. searchTagged/data2/mytag
     */
    public function searchTagged($tags)
    {
        $seperateTags = explode('/', $tags);
        $results = $this->searchOnTags($seperateTags);
        return new View($results, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/searchTryOut/{tags}/{order}/{state}/{page}/{size}", defaults={"order" = 1, "state"=0, "page"=0 , "size"=0})
     * E.g.
     * FF: /searchTryOut/{"tags":["data","tag2'"]}
     * postman: /searchTryOut/%7B%22tags%22:[%22data%22,%22tag2'%22]%7D
     */
    public function searchTryOut($tags, $order, $state, $page, $size)
    {

        $seperateTags = json_decode($tags, true);
        $seperateTags = array_values($seperateTags)[0];
        $results = $this->searchOnTags($seperateTags);

        return new View($results, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/searchTry2/{tags}/{order}/{state}/{page}/{size}", defaults={"order" = 1, "state"=0, "page"=0 , "size"=0})
     *
     * Get pagination, sort and status filter on tag search
     * Eg:
     * http://localhost/blogs/blog/web/app_dev.php/searchTry2/%7B%22tags%22:[%22data%22,%22data2%22,%22tag2'%22]%7D/1/0/2/2
     * Will give 2nd page of search results with a page size of 2 items  
     *
     */
    public function searchTry2($tags, $order, $state, $page, $size)
    {
        $seperateTags = json_decode($tags, true);
        $seperateTags = array_values($seperateTags)[0];

        $order = $this->getOrderedBy($order);
        $filter = $this->getStateFilter($state);

        $paginator = $this->getPagination($size, $page);
        $limit  = $paginator['limit'];
        $offset = $paginator['offset'];

        $results = $this->searchBlogpostsOnTags($seperateTags, $filter, $order, $limit, $offset);
        return new View($results, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/blogpost/{id}")
     */
    public function deleteAction($id)
    {
        $sn = $this->getDoctrine()->getManager();
        $blogPost = $this->getDoctrine()->getRepository('AppBundle:BlogPost')->find($id);
        if (empty($blogPost))
        {
            return new View("Post not found", Response::HTTP_NOT_FOUND);
        }
        else
        {
            $sn->remove($blogPost);
            $sn->flush();
        }

        $this->mail($blogPost->getPost(), "DELETE blogpost $id");
        return new View("deleted successfully", Response::HTTP_OK);
    }
    /**
     *
     *
     * @Rest\Get("/blogpostsTags/{tags}",
     *          requirements={"tags": "[a-zA-Z0-9\/]+"}
     *     )
     *
     * ToDo: pass tags as json
     */
    public function getByTagsAction($tags, Request $request)
    {
        $seperateTags = explode('/', $tags);
        $blogPost = $this->getDoctrine()->getRepository('AppBundle:BlogPost')->getAllByTags($seperateTags);
        return new View($blogPost, Response::HTTP_OK);

    }

}