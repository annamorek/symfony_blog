<?php
/**
 * StaticPagesController.
 *
 * @copyright (c) 2016 Anna Morek
 */
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class StaticPagesController.
 *
 * @Route(service="app.static_pages_controller")
 *
 * @package AppBundle\Controller
 * @author Anna Morek
 */
class StaticPagesController
{
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
     * Security Context
     *
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * Model object.
     *
     * @var ObjectRepository $postsModel
     */
    private $postsModel;

    /**
     * StaticPagesController constructor.
     * @param EngineInterface $templating
     * @param Translator $translator
     * @param SecurityContext $securityContext
     * @param ObjectRepository $postsModel
     */
    public function __construct(
        EngineInterface $templating,
        Translator $translator,
        SecurityContext $securityContext,
        ObjectRepository $postsModel
    ) {
        $this->templating = $templating;
        $this->translator = $translator;
        $this->securityContext = $securityContext;
        $this->postsModel = $postsModel;
    }

    /**
     * Index action.
     *
     * @Route("/", name="homepage")
     *
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function indexAction()
    {
        $posts = $this->postsModel->findAll();
        if (!$posts) {
            throw new NotFoundHttpException('Posts not found!');
        }
        return $this->templating->renderResponse(
            'AppBundle:staticPages:index.html.twig',
            array(
                'posts' => $posts
            )
        );
    }
}
