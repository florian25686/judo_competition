<?php

namespace CompetitionBundle\Controller;

use CompetitionBundle\Entity\Fighter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;


/**
 * Controller für die Erstellung und Verwaltung von Wettkämpfern
 * 
 */
class FighterController extends Controller
{
    /**
     * @Route("/fighter", name="fighterIndex")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $fighterRepository = $this->getDoctrine()
                        ->getRepository('CompetitionBundle:Fighter');
        
        $query = $fighterRepository->createQueryBuilder('f')
                            ->getQuery();
                
        $fighters = $query->getResult();
        
        // replace this example code with whatever you need
        return $this->render('fighter/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'fighters' => $fighters,
        ]);
    }
    
    /**
     * TODO: Create the form, block the groupaddition if someone is already in a group
     * @Route("/fighter/createFighter", name="createFighter")
     * @param Request $request
     */
    public function createFighterAction(Request $request) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $fighter = new Fighter();
        $fighter->setinFight(0);
        $fighter->setAgeGroup('0');
        $form = $this->createFormBuilder($fighter)
            ->add('lastName')
            ->add('firstName')
            ->add('weight')
            ->add('club')
            ->add('gender')
            ->add('groupId')
            ->add('save', SubmitType::class, array('label' => 'create.fighter.button'))
            ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted()) {
            
            $fighter = $form->getData();
            
            $em->persist($fighter);
            $em->flush();
            
            return $this->render('fighter/createFighter.html.twig', [
        	'form' => $form->createView(),
            ]);
        }
        
        
        return $this->render('fighter/createFighter.html.twig', [
        	'form' => $form->createView(),
	]);
    }
}
