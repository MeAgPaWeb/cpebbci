<?php

namespace AppBundle\Controller;


use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Advertising controller.
 *
 * @Route("/profile")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/", name="backend_profile_show")
     * @Method({"GET", "POST"})
     */
    public function showAction(Request $request)
    {
        $user = $this->getUser();

        $username = $user->getUsername();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $activeTab = null;

        /* PERSONAL DATA INIT */

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);
        $error = null;

        /* ACCOUNT INIT */

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $passDispatcher = $this->get('event_dispatcher');

        $passEvent = new GetResponseUserEvent($user, $request);
        $passDispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $passEvent);

        if (null !== $passEvent->getResponse()) {
            return $passEvent->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $passFormFactory = $this->get('fos_user.change_password.form.factory');

        $passForm = $passFormFactory->createForm();
        $passForm->setData($user);

        $passForm->handleRequest($request);

        /* PERSONAL DATA PROCESS */
        if ($request->request->get('personal') && $form->isValid()) {


            if ($request->request->get('custom_avatar') == 'false') {
                if (!isset(explode('_gender.jpg', $user->getAvatar())[1])) {
                    $this->get('app.uploader')->imageRemove($user->getAvatar(), $this->getParameter('media')['avatar']);
                }
                $user->setAvatar(null);
            } else if ($form['avatar']->getData() AND $error == NULL) {
                $error = $this->get('app.uploader')->imageValidate($form['avatar']->getData());
                if ($error == NULL) {
                    if (!isset(explode('_gender.jpg', $user->getAvatar())[1])) {
                        $this->get('app.uploader')->imageRemove($user->getAvatar(), $this->getParameter('media')['avatar']);
                    }
                    $newAvatarName = $this->get('app.uploader')->imageUpload($form['avatar']->getData(), $this->getParameter('media')['avatar'], array('width' => 80, 'height' => 80));
                    $user->setAvatar($newAvatarName);
                }
            }

            $user->setUsername($username);

            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('backend_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));


            //return $response;
        }

        /* ACCOUNT PROCESS */
        if ($request->request->get('account') && $passForm->isValid()) {

            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $passUserManager = $this->get('fos_user.user_manager');

            $passEvent = new FormEvent($passForm, $request);
            $passDispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $passEvent);

            $passUserManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('backend_profile_show');
                $response = new RedirectResponse($url);
            }
            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            //return $response;
        }

        if ($request->request->get('personal')) {
            $activeTab = 'personal';
        } else if ($request->request->get('account')) {
            $activeTab = 'account';
        }

        return $this->render('user/profile.html.twig', array(
            'profile' => true,
            'user' => $user,
            'form' => $form->createView(),
            'pass_form' => $passForm->createView(),
            'error' => $error,
            'active_tab' => $activeTab
        ));
    }

    /**
     * Edit the user
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('FOSUserBundle:Profile:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
