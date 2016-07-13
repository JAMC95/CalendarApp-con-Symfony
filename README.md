calendario
==========

A Symfony project created on July 10, 2016, 10:35 am.


Este programa permite tener una agenda con eventos, ordenados por categorias, es un ejercicio para practicar el MVC en Symfony


Lo más "complicado" de esta práctica es que la base de datos consta de dos tablas: event y category. A continuación explico como hice para "conectar" una tabla a la otra.

1-Una vez creada la tabla "Category", procedemos a crear la tabla "Event"
2-php bin/console doctrine:generate:entity
  Le ponemos de nombre "Appbundle:Event"
  Y los siguientes campos: {
  category de tipo integer
  name de tipo string
  details de tipo string
  day de tipo datatime
  street_address de tipo string
  city de tipo string
  zipcode de tipo String
  createtype de tipo datetime
  }
  en el archivo (Entity/Event.php)
  nos vamos a donde está declarado el integer,
  en la línea del @ORM\ escribimso:
  @ORM\ManyToOne(targetEntity="Category",inversedBy="events")
  guardamos los cambios con: php/bin/console doctrine:schema:update --force


  Ahora vamos a sacar los datos de esa tabla en un index:

  en Controller/EventController.php
  En la function indexAction escribimos:
    $events= $this -> getDoctrine()
              ->getRepository('AppBundle:Event')
              ->findAll();
          return $this->render('event/index.html.twig', [
              'events' => $events
          ]);

   En el event/index.html.twig escribimos:
 <table class="table table-striped">
                <thead>

                <tr>
                    <th>Nombre del evento</th>
                    <th>Categoria</th>
                    <th>Detalles</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {% for event in events %}
                <tr>
                    <td>{{ event.name }}</td>
                    <td>{{ event.category.name }}</td>
                    <td>{{ event.getDetails() }}</td>
                    <td>{{ event.day | date('F j, Y, g:i') }}</td>
                    <td><a href="/calendario/web/app_dev.php/event/edit/{{ event.id }}" class="btn btn-default">Editar</a>
                        <a href="/calendario/web/app_dev.php/event/delete/{{ event.id }}" class="btn btn-danger">Borrar</a></td>

                </tr>
                {% endfor %}
                </tbody>
            </table>

¿Y cómo añadimos un nuevo dato a Event desde la propia web creada?
Primero en Controller/EventController.php
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

Y en event/edit.html.twig simplemente con esto nos "escribirá" todo lo que hemos solicitado arriba
           <div class="panel-body">
                    {{ form_start(form) }}
                    {{ form_widget(form)}}
                    {{ form_end(form) }}
                </div>

¿Y para editarlo? Pues para ello escribiremos en Controller/EventController de nuevo, en editAction escribiremos:
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


Y ahora "de cara al público" tendremos que escribir lo siguiente (de nuevo) en event/edit.html.twig
              <div class="panel-body">
               {{ form_start(form) }}
               {{ form_widget(form)}}
                {{ form_end(form) }}
             </div>


Por último, explicaré como borrar un registro introducido en labase de datos, se hará con la sigueinte clase dentro de Event/Controller:
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

