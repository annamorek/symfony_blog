<?php

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
     * StaticPagesController constructor.
     * @param EngineInterface $templating
     * @param Translator $translator
     * @param SecurityContext $securityContext
     */
    public function __construct(
        EngineInterface $templating,
        Translator $translator,
        SecurityContext $securityContext
    ) {
        $this->templating = $templating;
        $this->translator = $translator;
        $this->securityContext = $securityContext;
    }

    /**
     * Index action.
     *
     * @Route("/", name="index")
     *
     * @throws NotFoundHttpException
     * @return Response A Response instance
     */
    public function indexAction()
    {

        return $this->templating->renderResponse(
            'AppBundle:staticPages:index.html.twig',
            array()
        );
    }
}