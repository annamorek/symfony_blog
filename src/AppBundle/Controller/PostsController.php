<?php

namespace AppBundle\Controller;

use AppBundle\Form\PostType;
use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class PostsController.
 *
 * @Route(service="app.posts_controller")
 *
 * @package AppBundle\Controller
 * @author Anna Morek
 */
class PostsController
{
    /**
     * Model object.
     *
     * @var ObjectRepository $postsModel
     */
    private $postsModel;

    /**
     * Model object.
     *
     * @var ObjectRepository $postsModel
     */
    private $tagsModel;

    /**
     * Form factory.
     *
     * @var
     */
    private $formFactory;

    /**
     * Routing object.
     *
     * @var RouterInterface $router
     */
    private $router;

    /**
     * Session object.
     *
     * @var Session $session
     */
    private $session;

    /**
     * Template engine.
     *
     * @var EngineInterface $templating
     */
    private $templating;

    /**
     * Translator object.
     *
     * @var Translator $translator
     */
    private $translator;

    /**
     * PostsController constructor.
     *
     * @param EngineInterface $templating Templating engine
     * @param ObjectRepository $postsModel Model object
     */
    public function __construct(
        ObjectRepository $postsModel,
        ObjectRepository $tagsModel,
        FormFactory $formFactory,
        RouterInterface $router,
        Session $session,
        EngineInterface $templating,
        Translator $translator
    ) {
        $this->postsModel = $postsModel;
        $this->tagsModel = $tagsModel;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->session = $session;
        $this->templating = $templating;
        $this->translator = $translator;
    }

    /**
     * Add action.
     *
     * @Route("/posts/add", name="posts-add")
     * @Route("/posts/add/")
     *
     * @param Request $request
     * @return Response A Response instance
     */
    public function addAction(Request $request)
    {
        $postForm = $this
            ->formFactory
            ->create(
                new PostType(),
                null,
                array(
                    'tag_model' => $this->tagsModel
                )
            );

        $postForm->handleRequest($request);

        if ($postForm->isValid()) {
            $post = $postForm->getData();
            $this->postsModel->save($post);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('posts.messages.post_added')
            );
            return new RedirectResponse(
                $this->router->generate('posts-index')
            );
        }

        return $this->templating->renderResponse(
            'AppBundle:posts:add.html.twig',
            array('form' => $postForm->createView())
        );
    }

    /**
     * Edit action.
     *
     * @Route("/posts/edit/{id}", name="posts-edit")
     * @Route("/posts/edit/{id}/", name="posts-edit")
     * @ParamConverter("post", class="AppBundle:Post")
     * @param Request $request
     * @return Response A Response instance
     */
    public function editAction(Request $request, Post $post = null)
    {
        $postForm = $this->formFactory->create(
            new PostType(),
            $post,
            array(
                'tag_model' => $this->tagsModel
            )
        );

        $postForm->handleRequest($request);

        if ($postForm->isValid()) {
            $post = $postForm->getData();
            $this->postsModel->save($post);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('posts.messages.success.edit')
            );
            return new RedirectResponse(
                $this->router->generate('posts-index')
            );
        }

        return $this->templating->renderResponse(
            'AppBundle:posts:edit.html.twig',
            array('form' => $postForm->createView())
        );
    }

    /**
     * Delete action.
     *
     * @Route("/posts/delete/{id}", name="posts-delete")
     * @Route("/posts/delete/{id}/", name="posts-delete")
     * @ParamConverter("post", class="AppBundle:Post")
     *
     * @param Request $request
     * @param Post|null $post
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Post $post = null)
    {
        $this->postsModel->delete($post);
        $this->session->getFlashBag()->set(
            'success',
            $this->translator->trans('posts.messages.success.delete')
        );
        return new RedirectResponse(
            $this->router->generate('posts-index')
        );
    }

    /**
     * @Route("posts/index", name="posts-index")
     * @Route("posts/index/", name="posts-index")
     */
    public function indexAction(Request $request)
    {
        $posts = $this->postsModel->findAll();
        if (!$posts) {
            throw new NotFoundHttpException('Posts not found!');
        }
        return $this->templating->renderResponse(
            'AppBundle:posts:index.html.twig',
            array('posts' => $posts)
        );
    }

    /**
     * View action.
     *
     * @Route("/posts/view/{id}", name="posts-view")
     * @Route("/posts/view/{id}/")
     * @ParamConverter("post", class="AppBundle:Post")
     * @param post $post Post entity
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function viewAction(Post $post = null)
    {
        return $this->templating->renderResponse(
            'AppBundle:posts:view.html.twig',
            array(
                'post' => $post,
            )
        );
    }
}
