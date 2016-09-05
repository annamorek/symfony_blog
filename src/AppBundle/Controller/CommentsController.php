<?php
/**
 * Comments controller.
 *
 * @copyright (c) 2016 Anna Morek
 */
namespace AppBundle\Controller;

use AppBundle\Form\CommentType;
use AppBundle\Entity\Comment;
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
 * Class CommentsController.
 *
 * @Route(service="app.comments_controller")
 *
 * @package AppBundle\Controller
 * @author Anna Morek
 */
class CommentsController
{
    /**
     * Model object.
     *
     * @var ObjectRepository $commentsModel
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
     * CommentsController constructor.
     * @param ObjectRepository $commentsModel
     * @param FormFactory $formFactory
     * @param RouterInterface $router
     * @param Session $session
     * @param EngineInterface $templating
     * @param Translator $translator
     * @param SecurityContext $securityContext
     */
    public function __construct(
        ObjectRepository $commentsModel,
        FormFactory $formFactory,
        RouterInterface $router,
        Session $session,
        EngineInterface $templating,
        Translator $translator,
        SecurityContext $securityContext
    ) {
        $this->commentsModel = $commentsModel;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->session = $session;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->securityContext = $securityContext;
    }

    /**
     * Add action.
     *
     * @Route("/comments/add", name="comments-add")
     * @Route("/comments/add/")
     *
     * @param Request $request
     * @return Response A Response instance
     */
    public function addAction(Request $request)
    {
        if ($this->securityContext->isGranted('IS_AUTHENTICATED_ANONYMOUSLY') == true) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('no_access')
            );
            return new RedirectResponse(
                $this->router->generate('homepage')
            );
        }
        $user = $this->securityContext->getToken()->getUser();

        $commentForm = $this
            ->formFactory
            ->create(
                new CommentType($user),
                null,
                array(
                    'validation_groups' => 'comment-default'
                )
            );

        $commentForm->handleRequest($request);

        if ($commentForm->isValid()) {
            $comment = $commentForm->getData();
            $this->commentsModel->save($comment);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('comments.messages.comment_added')
            );
            return new RedirectResponse(
                $this->router->generate('comments-index')
            );
        }

        return $this->templating->renderResponse(
            'AppBundle:comments:add.html.twig',
            array('form' => $commentForm->createView())
        );
    }

    /**
     * Edit action.
     *
     * @Route("/comments/edit/{id}", name="comments-edit")
     * @Route("/comments/edit/{id}/", name="comments-edit")
     * @ParamConverter("comment", class="AppBundle:Comment")
     * @param Request $request
     * @param Comment|null $comment
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Comment $comment = null)
    {
        if (!$comment) {
            throw new NotFoundHttpException('Comment not found!');
        }

        $postId = $comment->getPost()->getId();
        $user = $this->securityContext->getToken()->getUser();

        if ((int)($user->getId()) === (int)($comment->getUser()->getId())
            || $this->securityContext->isGranted('ROLE_ADMIN')) {
            $commentForm = $this->formFactory->create(
                new CommentType($user),
                $comment,
                array(
                    'validation_groups' => 'comment-default'
                )
            );

            $commentForm->handleRequest($request);

            if ($commentForm->isValid()) {
                $comment = $commentForm->getData();
                $this->commentsModel->save($comment);
                $this->session->getFlashBag()->set(
                    'success',
                    $this->translator->trans('comments.messages.success.edit')
                );
                if ($this->securityContext->isGranted('ROLE_ADMIN')) {
                    return new RedirectResponse(
                        $this->router->generate('admin-posts-view', array('id' => $postId))
                    );
                } else {
                    return new RedirectResponse(
                        $this->router->generate('posts-with-user-comments-view', array('id' => $postId))
                    );
                }
            }

            return $this->templating->renderResponse(
                'AppBundle:comments:edit.html.twig',
                array('form' => $commentForm->createView())
            );
        } else {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('no_access')
            );
            return new RedirectResponse(
                $this->router->generate('homepage')
            );
        }
    }

    /**
     * Delete action.
     *
     * @Route("/comments/delete/{id}", name="comments-delete")
     * @Route("/comments/delete/{id}/", name="comments-delete")
     * @ParamConverter("comment", class="AppBundle:Comment")
     *
     * @param Request $request
     * @param Comment|null $comment
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Comment $comment = null)
    {
        if (!$comment) {
            throw new NotFoundHttpException('Comment not found!');
        }

        $user = $this->securityContext->getToken()->getUser();

        if ((int)($user->getId()) === (int)($comment->getUser()->getId())
            || $this->securityContext->isGranted('ROLE_ADMIN')) {
            $this->commentsModel->delete($comment);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('comments.messages.success.delete')
            );
            if ($this->securityContext->isGranted('ROLE_ADMIN')) {
                return new RedirectResponse(
                    $this->router->generate('admin-comments-index')
                );
            } else {
                return new RedirectResponse(
                    $this->router->generate('posts-with-user-comments-view')
                );
            }
        } else {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('no_access')
            );
            return new RedirectResponse(
                $this->router->generate('homepage')
            );
        }
    }

    /**
     * Index action
     *
     * @Route("admin/comments/index", name="admin-comments-index")
     * @Route("admin/comments/index/", name="admin-comments-index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $comments = $this->commentsModel->findAll();
        return $this->templating->renderResponse(
            'AppBundle:comments:index.html.twig',
            array(
                'comments' => $comments
            )
        );
    }

    /**
     * View action
     *
     * @Route("admin/comments/view/{id}", name="admin-comments-view")
     * @Route("admin/comments/view/{id}/", name="admin-comments-view")
     * @param Request $request
     * @param Comment|null $comment
     * @return Response
     * @ParamConverter("comment", class="AppBundle:Comment")
     */
    public function viewAction(Request $request, Comment $comment = null)
    {
        return $this->templating->renderResponse(
            'AppBundle:comments:view.html.twig',
            array(
                'comment' => $comment
            )
        );
    }

    /**
     * Posts with suer comments.
     *
     * @Route("/comments/view/", name="posts-with-user-comments-view")
     * @Route("/comments/view", name="posts-with-user-comments-view")
     * @param Request $request
     * @return Response
     */
    public function viewPostsWithUserCommentsAction(Request $request)
    {
        if ($this->securityContext->isGranted('ROLE_USER')) {
            $user = $this->securityContext->getToken()->getUser();

            $comments = $user->getComments();

            return $this->templating->renderResponse(
                'AppBundle:comments:viewPostsWithUserComments.html.twig',
                array(
                    'user' => $user,
                    'comments' => $comments
                )
            );
        } else {
            throw new NotFoundHttpException('Page not found!');
        }
    }
}
