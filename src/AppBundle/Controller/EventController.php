<?php
/**
 * Created by PhpStorm.
 * User: jose
 * Date: 9/07/16
 * Time: 17:11
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class EventController extends Controller
{
    /**
     * @Route("/events" , name="event_list")
     */
    public function indexAction(Request $request){
        $events= $this -> getDoctrine()
            ->getRepository('AppBundle:Event')
            ->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events
        ]);
    }

    /**
     * @Route("/event/create" , name="event_create")
     */
    public function createAction(Request $request){
        $event= new Event;

        $form = $this->createFormBuilder($event)
            ->add('name', TextType::class,array('label'=>'Titulo','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('category', EntityType::class,array('class'=>'AppBundle:Category','label'=>'Nombre de la categoria', 'choice_label'=>'name','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('details', TextareaType::class,array('label'=>'Detalles','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('day', DateType::class,array('label'=>'Dia','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('city', TextType::class,array('label'=>'Ciudad','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('street_address', TextType::class,array('label'=>'Dirección','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('zipcode', TextType::class,array('label'=>'CP','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('save',SubmitType::class, array('label'=>'Crear evento','attr'=> array('class'=> 'btn btn-primary')))
            ->getForm();

        $form->handleRequest($request);
        //check submit

        if($form->isSubmitted() && $form->isValid()){
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $details = $form['details']->getData();
            $day = $form['day']->getData();
            $city = $form['city']->getData();
            $direccion = $form['street_address']->getData();
            $zipcode = $form['zipcode']->getData();
            // Ge Current Date and time
            $now = new \DateTime("now");
            //Las variables declaradas son introducidas a la base de datods de esta forma:
            $event->setName($name);
            $event->setCreateDate($now);
            $event->setDetails($details);
            $event->setCategory($category);
            $event->setDay($day);
            $event->setCity($city);
            $event->setStreetAddress($direccion);
            $event->setZipcode($zipcode);


            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
            $this->addFlash('notice',
                'Evengo guardado'
            );
            return $this->redirectToRoute('event_list');
        }


        return $this->render('event/create.html.twig', [
            'form' => $form -> createView()
        ]);
    }

    /**
     * @Route("/event/edit/{id}" , name="event_edit")
     */
    public function editAction($id,Request $request){
        $event= $this->getDoctrine()
            ->getRepository('AppBundle:Event')
            ->find($id);

        $form = $this->createFormBuilder($event)
            ->add('name', TextType::class,array('label'=>'Titulo','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('category', EntityType::class,array('class'=>'AppBundle:Category','label'=>'Nombre de la categoria', 'choice_label'=>'name','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('details', TextareaType::class,array('label'=>'Detalles','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('day', DateType::class,array('label'=>'Dia','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('city', TextType::class,array('label'=>'Ciudad','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('street_address', TextType::class,array('label'=>'Dirección','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('zipcode', TextType::class,array('label'=>'CP','attr'=>array('class'=>'form-control','style'=> 'margin-bottom:15px')))
            ->add('save',SubmitType::class, array('label'=>'Crear evento','attr'=> array('class'=> 'btn btn-primary')))
            ->getForm();

        $form->handleRequest($request);
        //check submit

        if($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $details = $form['details']->getData();
            $day = $form['day']->getData();
            $city = $form['city']->getData();
            $direccion = $form['street_address']->getData();
            $zipcode = $form['zipcode']->getData();
            // Ge Current Date and time
            $now = new \DateTime("now");
            //Las variables declaradas son introducidas a la base de datods de esta forma:
            $event->setName($name);
            $event->setCreateDate($now);
            $event->setDetails($details);
            $event->setCategory($category);
            $event->setDay($day);
            $event->setCity($city);
            $event->setStreetAddress($direccion);
            $event->setZipcode($zipcode);

            if ($form->isSubmitted() && $form->isValid()) {
                $name = $form['name']->getData();

                $em = $this->getDoctrine()->getManager();
                $category = $em->getRepository('AppBundle:Event')->find($id);

                $em->flush();

                $this->addFlash(
                    'notice',
                    'Evento guardado'
                );
                return $this->redirectToRoute('event_list');
            }
        }

        return $this->render('event/create.html.twig', [
            'form' => $form -> createView()
        ]);
    }
    /**
     * @Route("/event/delete/{id}" , name="event_delete")
     */
    public function deleteAction($id){
        $em =$this ->getDoctrine()->getManager();
        $event = $em-> getRepository('AppBundle:Event')->find($id);

        if(!$event)
            throw $this->createNotFoundException(
                'No encontrado'
            );

        $em->remove($event);
        $em->flush();

        $this->addFlash(
            'notice',
            'Evento borrado'
        );

        return $this->redirectToRoute('event_list');

    }
}