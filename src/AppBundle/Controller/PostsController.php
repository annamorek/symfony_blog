<?php

namespace AppBundle\Controller;

use AppBundle\Form\PostType;
use AppBundle\Form\CommentType;
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
     * Model object.
     *
     * @var ObjectRepository $postsModel
     */
    private $commentsModel;

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
     * SecurityContext object.
     *
     * @var SecurityContext $securityContext
     */
    private $securityContext;

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
        Translator $translator,
        SecurityContext $securityContext,
        ObjectRepository $commentsModel
    ) {
        $this->postsModel = $postsModel;
        $this->tagsModel = $tagsModel;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->session = $session;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->securityContext = $securityContext;
        $this->commentsModel = $commentsModel;
    }

    /**
     * Add action.
     *
     * @Route("admin/posts/add", name="admin-posts-add")
     * @Route("admin/posts/add/")
     *
     * @param Request $request
     * @return Response A Response instance
     */
    public function addAction(Request $request)
    {
        $this->checkAdmin();

        $postForm = $this
            ->formFactory
            ->create(
                new PostType(),
                null,
                array(
                    'tag_model' => $this->tagsModel,
                    'validation_groups' => 'post-default'
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
                $this->router->generate('admin-posts-index')
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
     * @Route("admin/posts/edit/{id}", name="admin-posts-edit")
     * @Route("admin/posts/edit/{id}/", name="admin-posts-edit")
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
                $this->router->generate('admin-posts-index')
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
     * @Route("admin/posts/delete/{id}", name="admin-posts-delete")
     * @Route("admin/posts/delete/{id}/", name="admin-posts-delete")
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
            $this->router->generate('admin-posts-index')
        );
    }

    /**
     * @Route("admin/posts/index", name="admin-posts-index")
     * @Route("admin/posts/index/", name="admin-posts-index")
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
     * @Route("admin/posts/{id}/comments", name="admin-posts-comments-index")
     * @Route("admin/posts/{id}/comments/", name="admin-posts-comments-index")
     * @ParamConverter("post", class="AppBundle:Post")
     */
    public function showCommentsAction(Request $request, Post $post = null)
    {
        if (!$post) {
            throw new NotFoundHttpException('Post not found!');
        }

        $comments = $post->getComments();

        return $this->templating->renderResponse(
            'AppBundle:posts:showComments.html.twig',
            array(
                'post' => $post,
                'comments' => $comments
            )
        );
    }

    /**
     * View action.
     *
     * @Route("admin/posts/view/{id}", name="admin-posts-view")
     * @Route("admin/posts/view/{id}/", name="admin-posts-view")
     * @Route("/posts/view/{id}/", name="posts-view")
     * @ParamConverter("post", class="AppBundle:Post")
     * @param post $post Post entity
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function viewAction(Request $request, Post $post = null)
    {

        if (!$post) {
            throw new NotFoundHttpException('Post not found!');
        }
        $user = $this->securityContext->getToken()->getUser();

        $postId = $post->getId();
        $user = $this->getUser();
        $comments = $post->getComments();

        if (($this->securityContext->isGranted('ROLE_USER') || ($this->securityContext->isGranted('ROLE_ADMIN'))))
        {
            $commentForm = $this
                ->formFactory
                ->create(
                    new CommentType($user),
                    null,
                    array()
                );

            $commentForm->handleRequest($request);

            if ($commentForm->isValid()) {
                $comment = $commentForm->getData();

                $comment->setPost($post);
                $comment->setUser($user);
                $this->commentsModel->save($comment);
                $this->session->getFlashBag()->set(
                    'success',
                    $this->translator->trans('comments.messages.success.add')
                );
                return new RedirectResponse(
                    $this->router->generate('posts-view', array('id' => $postId))
                );
            }

            return $this->templating->renderResponse(
                'AppBundle:posts:view.html.twig',
                array(
                    'post' => $post,
                    'form' => $commentForm->createView(),
                    'comments' => $comments
                )
            );
        } else {
            return $this->templating->renderResponse(
                'AppBundle:posts:view.html.twig',
                array(
                    'post' => $post,
                    'comments' => $comments
                )
            );
        }
    }

    /**
     * Get usr id
     *
     * @return int
     */
    private function getUser()
    {
        return $this->securityContext->getToken()->getUser();
    }

    private function checkAdmin()
    {
        $userRoles = $this->getRoles();
        $userRole = $userRoles[0]->getRole();

        if ($userRole !== 'ROLE_ADMIN') {
            $this->session->getFlashBag()->set(
                'notice',
                $this->translator->trans('no_access')
            );
            return new RedirectResponse(
                $this->router->generate('homepage')
            );
        }
    }

    private function getRoles()
    {
        return $this->securityContext->getToken()->getRoles();
    }
}
