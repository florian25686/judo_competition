<?php

namespace AppBundle\Controller;

use CompetitionBundle\Entity\Fighter;
use CompetitionBundle\Entity\Groups;
use CompetitionBundle\Form\FighterType;
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
        
        $ageGroupFighters = array();
        foreach ( $fighters as $fighter ) {
            $ageGroupFighters[$fighter->getAgeGroup()]['fighters'][] = $fighter;
        } 
        
        // replace this example code with whatever you need
        return $this->render('fighter/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'ageGroupFighters' => $ageGroupFighters,
        ]);
    }
    
    /**
     * TODO: Create the form, block the groupaddition if someone is already in a group
     * 
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
            ->add('ageGroup')
            ->add('club')
            ->add('gender')
            ->add('groupId')
            ->add('save', SubmitType::class, array('label' => 'create.fighter.button'))
            ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted()) {
            
            $fighter = $form->getData();
            // Check if group exists
            $group = $em->getRepository('CompetitionBundle:Groups')
                    ->find($fighter->getGroupId());
            
            if($group && $group->getStatus() == 1) {
                // Fighter add
                $group->addFighter($fighter);
                $em->persist($group);
                
            } elseif (!$group) {
                $group = new Groups();
                $group->addFighter($fighter);
                $group->setStatus(1);
                $group->setDeleted(NULL);
                $em->persist($group);
            }
            
            $em->persist($fighter);
            $em->flush();
            
            return $this->redirectToRoute('fighterIndex');
        }
        
            
        return $this->render('fighter/createFighter.html.twig', [
        	'form' => $form->createView(),
	]);
    }
    
     /**
     * @Route("/fighter/editFighter/{id}", name="editFighter")
     */
    public function editFighterAction(Request $request, $id=0)
    {
        $em = $this->getDoctrine()->getManager();
        
        $fighterRepository = $this->getDoctrine()
                        ->getRepository('CompetitionBundle:Fighter');
        
        $fighter = $fighterRepository->findOneById($id);
        
        $group = $em->getRepository('CompetitionBundle:Groups')
                    ->find($fighter->getGroupId());
        
       // Group has been printed and there some data shouldn't be changed easily
        $disabled_fields = false;
        
        if ($group->getStatus() == 2) 
        {
            $disabled_fields = true;
        }
        
        $form = $this->createForm(FighterType::class, $fighter)
            ->add('weight', null, array(
                'disabled' => $disabled_fields,
             ))
            ->add('gender', null, array(
                'disabled' => $disabled_fields,
            ))
            ->add('groupId', null, array(
                'disabled' => $disabled_fields
            ));
        
        
        $form->handleRequest($request);
        
        if($form->isSubmitted()) {
            
            $fighter = $form->getData();
            // Check if group exists
            
            
            if($group && $group->getStatus() == 1) {
                // Fighter add
                $group->addFighter($fighter);
                $em->persist($group);
            } 
            
            $em->persist($fighter);
            $em->flush();
            
            return $this->redirectToRoute('fighterIndex');
                
        }
        return $this->render('fighter/createFighter.html.twig', [
        	'form' => $form->createView(),
                'id'   => $id
	]);
    }
}
