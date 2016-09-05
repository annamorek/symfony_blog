<?php
/**
 * Users controller.
 *
 * @copyright (c) 2016 Anna Morek
 */
namespace AppBundle\Controller;

use AppBundle\Form\ChangeRoleType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use FOS\UserBundle\Doctrine\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormFactory;
use AppBundle\Entity\User;

/**
 * Class UsersController.
 *
 * @Route(service="app.users_controller")
 *
 * @package AppBundle\Controller
 * @author Anna Morek
 */
class UsersController
{
    /**
     * User Manager.
     *
     * @var EngineInterface $userManager
     */
    private $userManager;

    /**
     * Template engine.
     *
     * @var EngineInterface $templating
     */

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
     * Form factory
     *
     * @var $formFactory
     */
    private $formFactory;

    /**
     *
     * USER
     * @var
     */
    private $userModel;

    /**
     * SecurityContext object.
     *
     * @var SecurityContext $securityContext
     */
    private $securityContext;

    /**
     * UsersController constructor.
     * @param EngineInterface $templating
     * @param UserManager $userManager
     * @param FormFactory $formFactory
     * @param RouterInterface $router
     * @param Translator $translator
     * @param Session $session
     * @param ObjectRepository $userModel
     * @param SecurityContext $securityContext
     */
    public function __construct(
        EngineInterface $templating,
        UserManager $userManager,
        FormFactory $formFactory,
        RouterInterface $router,
        Translator $translator,
        Session $session,
        ObjectRepository $userModel,
        SecurityContext $securityContext
    ) {
        $this->templating = $templating;
        $this->userManager = $userManager;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->translator = $translator;
        $this->session = $session;
        $this->userModel = $userModel;
        $this->securityContext = $securityContext;
    }

    /**
     * Index action.
     *
     * @Route("/admin/users/index/", name="admin-users-index")
     * @Route("/admin/users/index")
     *
     * @param Request $request
     * @return Response A Response instance
     */
    public function indexAction()
    {
        $currentUserId = $this->getUserId();

        $users = $this->userManager->findUsers();
        return $this->templating->renderResponse(
            'AppBundle:users:index.html.twig',
            array(
                'users' => $users,
                'current_user_id' => $currentUserId
            )
        );
    }

    /**
     * Add action.
     *
     * @Route("/admin/users/add", name="admin-users-add")
     * @Route("/admin/users/add")
     * @param Request $request
     * @param User|null $user
     * @return RedirectResponse
     */
    public function addAction(Request $request)
    {
        $userForm = $this->formFactory->create(
            new UserType(),
            null,
            array(
            )
        );

        $userForm->handleRequest($request);

        if ($userForm->isValid()) {
            $user = $userForm->getData();
            $this->userModel->save($user);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('users.messages.success.added')
            );
        }
        return $this->templating->renderResponse(
            'AppBundle:users:add.html.twig',
            array('form' => $userForm->createView())
        );
    }

    /**
     * Edit action.
     *
     * @Route("/admin/users/edit/{id}", name="admin-users-edit")
     * @Route("/admin/users/edit/{id}")
     * @ParamConverter("user", class="AppBundle:User")
     * @param Request $request
     * @param User|null $user
     * @return RedirectResponse
     */
    public function editAction(Request $request, User $user = null)
    {

        if (!$user) {
            throw new NotFoundHttpException('User not found!');
        }

        $userForm = $this->formFactory->create(
            new UserType(),
            $user,
            array(
                'edit' => true
            )
        );

        $userForm->handleRequest($request);

        if ($userForm->isValid()) {
            $user = $userForm->getData();
            $this->userManager->updateUser($user);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('users.messages.success.edit')
            );
        }
        return $this->templating->renderResponse(
            'AppBundle:users:edit.html.twig',
            array('form' => $userForm->createView())
        );
    }

    /**
     * Delete action.
     *
     * @Route("admin/users/delete/{id}", name="admin-users-delete")
     * @Route("admin/users/delete/{id}/", name="admin-users-delete")
     * @ParamConverter("user", class="AppBundle:User")
     *
     * @param Request $request
     * @param User|null $user
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, User $user = null)
    {
        $this->userModel->delete($user);
        $this->session->getFlashBag()->set(
            'success',
            $this->translator->trans('users.messages.success.delete')
        );
        return new RedirectResponse(
            $this->router->generate('admin-users-index')
        );
    }

    /**
     * Change user role
     *
     * @Route("/admin/users/editRole/{id}", name="admin-user-edit-role")
     * @Route("/admin/users/editRole/{id}")
     * @ParamConverter("user", class="AppBundle:User")
     * @param Request $request
     * @param User|null $user
     * @return RedirectResponse
     */
    public function editRoleAction(Request $request, User $user = null)
    {
        $currentUserId = $this->getUserId();

        //sprawdza, czy user istnieje oraz czy user do zmiany roli nie jest zalogowany
        if (!$user) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('admin.user_role.not_found')
            );
            return new RedirectResponse(
                $this->router->generate('admin-users-index')
            );
        } elseif ($currentUserId === (int)$user->getId()) {
            $this->session->getFlashBag()->set(
                'warning',
                $this->translator->trans('user.messages.cannot_change_role_currently_logged_id')
            );
            return new RedirectResponse(
                $this->router->generate('admin-users-index')
            );
        }

        $changeRoleForm = $this->formFactory->create(
            new ChangeRoleType()
        );

        $changeRoleForm->handleRequest($request);

        if ($changeRoleForm->isValid()) {
            $choosenRole = $changeRoleForm->getData();
            $user->setRoles(array($choosenRole['role']));
            $this->userManager->updateUser($user);
            $this->session->getFlashBag()->set(
                'success',
                $this->translator->trans('admin.user_role.change.success')
            );

            return new RedirectResponse(
                $this->router->generate('admin-users-index')
            );
        }
        return $this->templating->renderResponse(
            'AppBundle:users:changeRole.html.twig',
            array('form' => $changeRoleForm->createView())
        );
    }

    /**
     * Returns user Id
     *
     * @return int
     */
    private function getUserId()
    {
        return (int)$this->securityContext->getToken()->getUser()->getId();
    }
}
