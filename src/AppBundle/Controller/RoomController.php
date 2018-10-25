<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Room;
use AppBundle\Entity\DataLogger;
use AppBundle\Entity\Library;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Room controller.
 *
 * @Route("room")
 */
class RoomController extends Controller
{
    /**
     * Lists all room entities.
     *
     * @Route("/{library}", name="room_index")
     * @Method("GET")
     */
    public function indexAction(Request $request, Library $library)
    {
        $em = $this->getDoctrine()->getManager();

        $rooms = $em->getRepository('AppBundle:Room')->findByLibrary($library);

        return $this->render('room/index.html.twig', array(
            'rooms' => $rooms,
        ));
    }

    /**
     * Creates a new room entity.
     *
     * @Route("/{library}/new", name="room_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Library $library)
    {
        $route=$request->request->get('route');
        $room = new Room();
        $form = $this->createForm('AppBundle\Form\RoomType', $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $room->setLibrary($library);
            $em->persist($room);
            $library->addRoom($room);
            $em->persist($library);
            $em->flush(); //se rompe acá wachon!!!
            $this->loadDataAction($request, $room);

            return $this->redirectToRoute($route, array('request' => $request, 'library' => $library->getId()));
        }

        return $this->render('room/new.html.twig', array(
            'room' => $room,
            'form' => $form->createView(),

        ));
    }

    /**
     * Finds and displays a room entity.
     *
     * @Route("/{id}", name="room_show")
     * @Method("GET")
     */
    public function showAction(Room $room)
    {
        $deleteForm = $this->createDeleteForm($room);

        return $this->render('room/show.html.twig', array(
            'room' => $room,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing room entity.
     *
     * @Route("/{id}/edit", name="room_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Room $room)
    {
        $deleteForm = $this->createDeleteForm($room);
        $editForm = $this->createForm('AppBundle\Form\RoomType', $room);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->loadDataAction($request, $room);

            return $this->redirectToRoute('room_edit', array('id' => $room->getId()));
        }

        return $this->render('room/edit.html.twig', array(
            'room' => $room,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a room entity.
     *
     * @Route("/{id}", name="room_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Room $room)
    {
        $form = $this->createDeleteForm($room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($room);
            $em->flush();
        }

        return $this->redirectToRoute('room_index');
    }

    /**
     * Creates a form to delete a room entity.
     *
     * @param Room $room The room entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Room $room)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('room_delete', array('id' => $room->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


    public function loadDataAction(Request $request, Room $room){
      $file = $request->files->get('upload');
      $name = $file->getFilename();
      $url =  $file->getRealPath();
      $em = $this->getDoctrine()->getManager();
      if ($file->isValid()) {
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject($url);
        $phpExcelObject->setActiveSheetIndex(0);
        $activesheet = $phpExcelObject->getActiveSheet()->toArray();
        $j=5;
        while (isset($activesheet[$j][0])) {
          $data = new DataLogger();
          $data->setNumber($activesheet[$j][0]);
          $data->setRoom($room);
          $data->setDate(\DateTime::createFromFormat( "d/m/Y H:i:s A", $activesheet[$j][1]." ".$activesheet[$j][2]));
          $data->setTemperature($activesheet[$j][3]);
          $data->setRh($activesheet[$j][4]);
          $data->setDewpt($activesheet[$j][5]);
          $em->persist($data);
          // $em->flush();
          $room->addDataLogger($data);
          $em->persist($room);
          $j++;
        }
        $em->flush();
        return true;
      }
    }
}
