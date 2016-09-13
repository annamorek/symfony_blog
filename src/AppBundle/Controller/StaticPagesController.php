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
     * @Route("/{page}", defaults={"page": 1}, requirements={"page": "\d+" }, name="homepage")
     * @param int $page
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function indexAction($page = 1)
    {
        $posts = $this->postsModel->getAllPosts($page);
        if (!$posts) {
            throw new NotFoundHttpException('Posts not found!');
        }

        $totalPosts = $posts->count();
        $iterator = $posts->getIterator();
        $limit = 3;
        $maxPages = ceil($totalPosts / $limit);
        $thisPage = $page;

        return $this->templating->renderResponse(
            'AppBundle:staticPages:index.html.twig',
            array(
                'posts' => $posts,
                'maxPages' => $maxPages,
                'thisPage' => $thisPage
            )
        );
    }
}
