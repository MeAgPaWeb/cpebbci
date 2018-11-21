<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Library;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Library controller.
 *
 * @Route("library")
 */
class LibraryController extends Controller
{
    /**
     * Lists all library entities.
     *
     * @Route("/", name="library_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $libraries = $em->getRepository('AppBundle:Library')->findAll();

        return $this->render('library/index.html.twig', array(
            'libraries' => $libraries,
        ));
    }

    /**
     * Lists all library entities.
     *
     * @Route("/{id}/log", name="library_log")
     * @Method("GET")
     */
    public function statisticAction(Library $library)
    {
        $em = $this->getDoctrine()->getManager();

        $libraries = $em->getRepository('AppBundle:Library')->findAll();

        return $this->render('library/statistics.html.twig', array(
            'libraries' => $libraries,
            'library' => $library
        ));
    }

    /**
     * get  all library entities.
     *
     * @Route("/api/list", name="api_library_list")
     * @Method("GET")
     */
    public function apiListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $libraries = $em->getRepository('AppBundle:Library')->findAll();
        $response = new Response();
        $response->setContent(json_encode($libraries));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /**
     * Creates a new library entity.
     *
     * @Route("/new", name="library_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $route=$request->request->get('route');
        $library = new Library();
        $form = $this->createForm('AppBundle\Form\LibraryType', $library);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $library->setOwner($this->getUser());
            //$library->addFollower($this->getUser());
            $em->persist($library);
            $em->flush();
            //$this->getUser()->addFollowing($library);
            $em->persist($this->getUser());
            $em->flush();

            return $this->redirectToRoute($route, array('request' => $request,'library' => $library->getId()));
        }

        return $this->render('library/new.html.twig', array(
            'library' => $library,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a library entity.
     *
     * @Route("/{id}", name="library_show")
     * @Method("GET")
     */
    public function showAction(Library $library)
    {

        return $this->render('library/show.html.twig', array(
            'library' => $library,
        ));
    }

    /**
     * Displays a form to edit an existing library entity.
     *
     * @Route("/{id}/edit", name="library_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Library $library)
    {
        //$deleteForm = $this->createDeleteForm($library);
        $editForm = $this->createForm('AppBundle\Form\LibraryType', $library);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('library_edit', array('id' => $library->getId()));
        }

        return $this->render('library/edit.html.twig', array(
            'library' => $library,
            'edit_form' => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a library entity.
     *
     * @Route("/{id}/delete", name="library_delete")
     * @Method("GET")
     */
    public function deleteAction(Library $library)
    {
        $em = $this->getDoctrine()->getManager();
        $library->setEnabled(false);
        $em->persist($library);
        $em->flush();

        return $this->redirectToRoute('homepage');
    }
}
