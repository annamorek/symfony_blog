<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



/**
 * Class PostsController.
 *
 * @Route(service="app.admin_posts_controller")
 *
 * @package AppBundle\Controller
 * @author Anna Morek
 */
class PostsController
{
    /**
     * Template engine.
     *
     * @var EngineInterface $templating
     */
    private $templating;

    /**
     * Model object.
     *
     * @var ObjectRepository $model
     */
    private $model;

    /**
     * PostsController constructor.
     *
     * @param EngineInterface $templating Templating engine
     * @param ObjectRepository $model Model object
     */
    public function __construct(
        EngineInterface $templating,
        ObjectRepository $model
    ) {
        $this->templating = $templating;
        $this->model = $model;
    }

    /**
     * @Route("/posts/index", name="admin-posts-index")
     * @Route("/posts/index/", name="admin-posts-index")
     */
    public function indexAction(Request $request)
    {
        $posts = $this->model->findAll();
        if (!$posts) {
            throw new NotFoundHttpException('Posts not found!');
        }
        return $this->templating->renderResponse(
            'AppBundle:posts:index.html.twig',
            array('posts' => $posts)
        );
    }
}
