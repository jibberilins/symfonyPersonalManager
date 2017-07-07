<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class todoController extends Controller
{
    /** These are our url routes
	 *
     * @Route("/todos", name="todo_list")
     */
    public function listAction(){
		//Passes values from the Todo Mysql table into the $todos value
		$todos = $this->getDoctrine()
					 ->getRepository('AppBundle:Todo')
					 ->findAll();
		//Loading the todo view in here...
        return $this->render('todo/index.html.twig', array(
			'todos' => $todos
		));
    }
	
	
	/** These are our url routes
	 *
     * @Route("/todo/calender", name="todo_calender")
     */
    public function calenderAction(){
		
		//Will need to pass the todo dates from each todo in here and place onto the calender
		
		//$todos = $this->getDoctrine()
					 //->getRepository('AppBundle:Todo')
					 //->findAll();
		//Loading the todo view in here...
        return $this->render('todo/calender.html.twig', array());
    }
	
	
	/** These are our url routes
	 *
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request){
		$todo = new Todo;
		
		$form = $this->createFormBuilder($todo)
					 ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
					 ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
					 ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
					 ->add('priority', ChoiceType::class, array('choices' => array('Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
					 ->add('due_date', DateTimeType::class, array('attr' => array('class' => 'formcontrol', 'style' => 'margin-bottom:15px')))
					 ->add('save', SubmitType::class, array('label' => 'Create Todo', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
					 ->getForm();
		
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()){
			//Get the data
			$name = $form['name']->getData();
			$category = $form['category']->getData();
			$description = $form['description']->getData();
			$priority = $form['priority']->getData();
			$due_date = $form['due_date']->getData();
			
			$now = new\DateTime('now');
			
			//Set the retrieved form data
			$todo->setName($name);
			$todo->setCategory($category);
			$todo->setDescription($description);
			$todo->setPriority($priority);
			$todo->setDueDate($due_date);
			$todo->setCreateDate($now);
			
			//Pass the data into the database
			$em = $this->getDoctrine()->getManager();
			$em->persist($todo);
			$em->flush();
			
			//Add msg
			$this->addFlash(
				'notice',
				'Todo Added'
			);
			
			//Redirect to correct page
			return $this->redirectToRoute('todo_list');
		}
		
		//Loading the todo view in here...
        return $this->render('todo/create.html.twig', array(
			'form' => $form->createView()
		));
    }
	
	
	/** These are our url routes
	 * id -- We need to know which item to edit
	 *
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request){
		//Passes values from the Todo Mysql table into the $todo value
		$todo = $this->getDoctrine()
					 ->getRepository('AppBundle:Todo')
					 ->find($id);
		
		//Build the form using this data in todo
		$form = $this->createFormBuilder($todo)
					 ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
					 ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
					 ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
					 ->add('priority', ChoiceType::class, array('choices' => array('Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
					 ->add('due_date', DateTimeType::class, array('attr' => array('class' => 'formcontrol', 'style' => 'margin-bottom:15px')))
					 ->add('save', SubmitType::class, array('label' => 'Update Todo', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
					 ->getForm();
		
		$form->handleRequest($request);
		
		//Once the form is edited...
		if($form->isSubmitted() && $form->isValid()){
			//Get the new edited data
			$name = $form['name']->getData();
			$category = $form['category']->getData();
			$description = $form['description']->getData();
			$priority = $form['priority']->getData();
			$due_date = $form['due_date']->getData();
			
			$now = new\DateTime('now');
			
			//Find the record in the DB...
			$em = $this->getDoctrine()->getManager();
			$todo = $em->getRepository('AppBundle:Todo')->find($id);
			
			//Set the new/edited retrieved form data
			$todo->setName($name);
			$todo->setCategory($category);
			$todo->setDescription($description);
			$todo->setPriority($priority);
			$todo->setDueDate($due_date);
			$todo->setCreateDate($now);
			
			//Pass the edited data into the database
			$em->flush();
			
			//Add msg
			$this->addFlash(
				'notice',
				'Todo Updated'
			);
			
			//Redirect to correct page
			return $this->redirectToRoute('todo_list');
		}
		
		//Loading the todo view in here...
        return $this->render('todo/edit.html.twig', array(
			'todo' => $todo,
			'form' => $form->createView()
		));
    }
	
	
	/** These are our url routes
	 * id -- We need to know which item to get the details of
	 *
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id, Request $request){
		//Passes values from the Todo Mysql table into the $todo value
		$todo = $this->getDoctrine()
					 ->getRepository('AppBundle:Todo')
					 ->find($id);
		//Loading the todo view in here...
        return $this->render('todo/details.html.twig', array(
			'todo' => $todo
		));
    }
	
	
	/** These are our url routes
	 * id -- We need to know which item to delete
	 *
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id, Request $request){
		//Find the record in the DB...
		$em = $this->getDoctrine()->getManager();
		$todo = $em->getRepository('AppBundle:Todo')->find($id);
		
		//Delete
		$em->remove($todo);
		$em->flush();
		
		//Add msg
		$this->addFlash(
			'notice',
			'Todo Deleted'
		);
		
		//Redirect to correct page
		return $this->redirectToRoute('todo_list');
    }
}