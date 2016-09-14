<?php
/**
 * Tags controller class.
 *
 * @copyright (c) 2016 Tomasz Chojna
 * @link http://epi.chojna.info.pl
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Tag;
use AppBundle\Form\TagType;
use Doctrine\Common\Persistence\ObjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class TagsController.
 *
 * @Route(service="app.tags_controller")
 *
 * @package AppBundle\Controller
 * @author Tomasz Chojna
 */
class TagsController
{
    /**
     * Translator object.
     *
     * @var Translator $translator
     */
    private $translator;

    /**
     * Template engine.
     *
     * @var EngineInterface $templating
     */
    private $templating;

    /**
     * Session object.
     *
     * @var Session $session
     */
    private $session;

    /**
     * Routing object.
     *
     * @var RouterInterface $router
     */
    private $router;

    /**
     * Model object.
     *
     * @var ObjectRepository $model
     */
    private $model;

    /**
     * Form factory.
     *
     * @var FormFactory $formFactory
     */
    private $formFactory;

    /**
     * TagsController constructor.
     * @param ObjectRepository $model
     * @param FormFactory $formFactory
     * @param RouterInterface $router
     * @param Session $session
     * @param EngineInterface $templating
     * @param Translator $translator
     */
    public function __construct(
        ObjectRepository $model,
        FormFactory $formFactory,
        RouterInterface $router,
        Session $session,
        EngineInterface $templating,
        Translator $translator
    ) {
        $this->translator = $translator;
        $this->templating = $templating;
        $this->session = $session;
        $this->router = $router;
        $this->model = $model;
        $this->formFactory = $formFactory;
    }

    /**
     * Index action.
     *
     * @Route("admin/tags/index", name="admin-tags-index")
     * @Route("admin/tags/index/", name="admin-tags-index")
     * @Route("/tags/index/", name="tags-index")
     *
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function indexAction()
    {
        $tags = $this->model->findAll();
        if (!$tags) {
            throw new NotFoundHttpException(
                $this->translator->trans('tags.messages.tags_not_found')
            );
        }
        return $this->templating->renderResponse(
            'AppBundle:tags:index.html.twig',
            array('tags' => $tags)
        );
    }

    /**
     * View action.
     *
     * @Route("admin/tags/view/{id}", name="admin-tags-view")
     * @Route("admin/tags/view/{id}/", name="admin-tags-view")
     * @Route("/tags/view/{id}/",  name="tags-view")
     * @ParamConverter("tag", class="AppBundle:Tag")
     *
     * @param Tag $tag Tag entity
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function viewAction(Tag $tag = null)
    {
        if (!$tag) {
            throw new NotFoundHttpException(
                $this->translator->trans('tags.messages.tag_not_found')
            );
        }
        $posts = $tag->getPosts();
        return $this->templating->renderResponse(
            'AppBundle:tags:view.html.twig',
            array(
                'tag' => $tag,
                'posts' => $posts
            )
        );
    }

    /**
     * Add action.
     *
     * @Route("admin/tags/add", name="admin-tags-add")
     * @Route("admin/tags/add/")
     *
     * @param Request $request
     * @return Response A Response instance
     */
    public function addAction(Request $request)
    {
        $tagForm = $this->formFactory->create(
            new TagType(),
            null,
            array(
                'validation_groups' => 'tag-default'
            )
        );

        $tagForm->handleRequest($request);

        if ($tagForm->isValid()) {
            $tag = $tagForm->getData();
            $this->model->save($tag);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('tags.messages.success.add')
            );
            return new RedirectResponse(
                $this->router->generate('admin-tags-index')
            );
        }

        return $this->templating->renderResponse(
            'AppBundle:tags:add.html.twig',
            array('form' => $tagForm->createView())
        );
    }

    /**
     * Edit action.
     *
     * @Route("admin/tags/edit/{id}", name="admin-tags-edit")
     * @Route("admin/tags/edit/{id}/")
     * @ParamConverter("tag", class="AppBundle:Tag")
     *
     * @param Request $request
     * @param Tag|null $tag
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Tag $tag = null)
    {
        if (!$tag) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('tags.messages.tag_not_found')
            );
            return new RedirectResponse(
                $this->router->generate('admin-tags-index')
            );
        }

        $tagForm = $this->formFactory->create(
            new TagType(),
            $tag,
            array(
                'validation_groups' => 'tag-default'
            )
        );

        $tagForm->handleRequest($request);

        if ($tagForm->isValid()) {
            $tag = $tagForm->getData();
            $this->model->save($tag);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('tags.messages.success.edit')
            );
            return new RedirectResponse(
                $this->router->generate('tags-index')
            );
        }

        return $this->templating->renderResponse(
            'AppBundle:tags:edit.html.twig',
            array('form' => $tagForm->createView())
        );

    }

    /**
     * Delete action.
     *
     * @Route("admin/tags/delete/{id}", name="admin-tags-delete")
     * @Route("admin/tags/delete/{id}/")
     * @ParamConverter("tag", class="AppBundle:Tag")
     *
     * @param Request $request
     * @param Tag|null $tag
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Tag $tag = null)
    {
        if (!$tag) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('tags.messages.tag_not_found')
            );
            return new RedirectResponse(
                $this->router->generate('tags-index')
            );
        }

        $tagForm = $this->formFactory->create(
            new TagType(),
            $tag,
            array(
                'validation_groups' => 'tag-delete'
            )
        );

        $tagForm->handleRequest($request);

        if ($tagForm->isValid()) {
            if ($tagForm->get('Tak')->isClicked()) {
                $this->model->delete($tag);
                $this->session->getFlashBag()->set(
                    'success',
                    $this->translator->trans('tags.messages.success.delete')
                );
                return new RedirectResponse(
                    $this->router->generate('tags-index')
                );
            } else {
                return new RedirectResponse(
                    $this->router->generate('tags-index')
                );
            }
        }

        return $this->templating->renderResponse(
            'AppBundle:tags:delete.html.twig',
            array(
                'form' => $tagForm->createView(),
                'tag' => $tag
            )
        );
    }
}
