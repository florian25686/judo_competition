<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Fighter;
use AppBundle\Entity\Groups;
use AppBundle\Form\FighterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller für die Erstellung und Verwaltung von Wettkämpfern
 *
 */
class FighterController extends Controller
{
    /**
     * @Route("/fighter/", name="fighterIndex")
     */
    public function indexAction(Request $request)
    {
        $fighters = $this->loadAllFightersFromRepository();
        
        $fighterCountPerGroups = array();
        $ageGroupFighters = array();
        foreach ($fighters as $fighter) {
            $ageGroupFighters[$fighter->getAgeGroup()->getName()]['fighters'][] = $fighter;
        }
        
        ksort($ageGroupFighters);
        
        $groupsRepository = $this->getDoctrine()
                        ->getRepository('AppBundle:Groups');
        
        $groups = $groupsRepository
                ->findBy(array('deleted' => 0));
        
        foreach($groups as $group) {
            $fighterNumber = count($group->getFighters());
            $minWeightInGroup = 200;
            $maxWeightInGroup = 0;
            foreach($group->getFighters() as $fighter) {
                $weight = $fighter->getWeight();
                if($weight < $minWeightInGroup) {
                    $minWeightInGroup = $weight;
                } elseif ($weight > $maxWeightInGroup) {
                    $maxWeightInGroup = $weight;
                }
            }
            
            $fighterCountPerGroups[$group->getAgeGroup()->getName()]['groups'][] = array(
                                                                                    'id' => $group->getId(),
                                                                                    'numberFighter' => $fighterNumber,
                                                                                     'minWeightFighter' => $minWeightInGroup,
                                                                                     'maxWeightFighter' => $maxWeightInGroup
                                                                                    );
         }
         if(null !== $request->get('sortField')) {
            $sortField = $request->get('sortField');
         } else {
             $sortField = "weight";
         }

        return $this->render('fighter/index.html.twig', [
            'ageGroupFighters' => $ageGroupFighters,
            'fighterCountPerGroups' => $fighterCountPerGroups,
            'sortField' => $sortField
        ]);
    }

    
    private function loadAllFightersFromRepository()
    {

        $fighterRepository = $this->getDoctrine()
                        ->getRepository('AppBundle:Fighter');

        $query = $fighterRepository->createQueryBuilder('f')
                            ->where('f.deleted = 0')
                            ->orderBy('f.gender', 'ASC')
                            ->addOrderBy('f.weight', 'ASC')
                            ->getQuery();

        $fighters = $query->getResult();

        return $fighters;
    }

    /**
     * TODO: Create the form, block the groupaddition if someone is already in a group
     *
     * @Route("/fighter/createFighter", name="createFighter")
     * @param Request $request
     * s
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createFighterAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_WEIGHT', null, 'Unable to access this page!');
        
        $em = $this->getDoctrine()->getManager();

        $fighter = new Fighter();
        $fighter->setinFight(0);
        
        $form = $this->createForm(FighterType::class, $fighter);

        if (true == $this->get('security.authorization_checker')->isGranted('ROLE_WEIGHT')
            && false == $this->get('security.authorization_checker')->isGranted('ROLE_MANAGEMENT')
        ) {
            $form->add('groups', null, array(
                'disabled' => true
            ));
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $fighter = $form->getData();
            if ($fighter->getGroupId()) {
                // Check if group exists
                $group = $em->getRepository('AppBundle:Groups')
                        ->find($fighter->getGroupId());

                if ($group->getId() && $group->getStatus() == 1) {
                    $group->addFighter($fighter);
                    $em->persist($group);
                } elseif (!$group) {
                    $group = new Groups();
                    $group->addFighter($fighter);
                    $group->setStatus(1);
                    $group->setDeleted(null);
                    $em->persist($group);
                }
            }
            $em->persist($fighter);
            $em->flush();
            $this->addFlash('success', 'message.fighter.created');
            return $this->redirectToRoute('createFighter');
        }


        return $this->render('fighter/createFighter.html.twig', [
            'form' => $form->createView(),
    ]);
    }

    /**
     * @Route("/fighter/editFighter/{id}", name="editFighter")
     * @param Request $request
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editFighterAction(Request $request, $id=0)
    {
        
        $this->denyAccessUnlessGranted('ROLE_WEIGHT', null, 'Unable to access this page!');
        
        $em = $this->getDoctrine()->getManager();

        $fighterRepository = $this->getDoctrine()
                        ->getRepository('AppBundle:Fighter');

        $fighter = $fighterRepository->findOneById($id);
        $group = $fighter->getGroups();

       // Group has been printed and there some data shouldn't be changed easily
        $disabled_fields = false;
        if ($group && $group->getStatus() == 2) {
            $disabled_fields = true;
        } elseif ($group && $group->getStatus() == 1) {
            $group->addFighter($fighter);
                $em->persist($group);
        }

        if(true ==$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            $disabled_fields = false;
        }

        $form = $this->createForm(FighterType::class, $fighter)
            ->add('weight', null, array(
                'disabled' => $disabled_fields,
             ))
            ->add('gender', null, array(
                'disabled' => $disabled_fields,
            ))
            ->add('birthDate', ChoiceType::class, array(
                'choices'  => array(
                    '1999' => '1999',
                    '2000' => '2000',
                    '2001' => '2001',
                    '2002' => '2002',
                    '2003' => '2003',
                    '2004' => '2004',
                    '2005' => '2005',
                    '2006' => '2006',
                    '2007' => '2007',
                    '2008' => '2008',
                    '2009' => '2009',
                    '2010' => '2010',
                    '2011' => '2011',
                ),
                'disabled' => $disabled_fields,
            ));

        if (true ==$this->get('security.authorization_checker')->isGranted('ROLE_WEIGHT')
            && false ==$this->get('security.authorization_checker')->isGranted('ROLE_MANAGEMENT')
        ) {
            $form->add('groups', null, array(
                'disabled' => true
            ));
        } else {
            $form->add('groups', EntityType::class, array(
                'class' => 'AppBundle:Groups',
                'choice_label' => 'AgeGroupChoice',
                'disabled' => $disabled_fields
            ));
        }
        $form->add('save', SubmitType::class, array('label' => 'edit.fighter.button'));

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $fighter = $form->getData();

            if ($group && $group->getStatus() == 1) {
                // Fighter add
                $group->addFighter($fighter);
                $em->persist($group);
            } else {
                
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
    
    /**
     * @Route("/fighter/deleteFighter/{id}", name="deleteFighter")
     */
    public function deleteFighter($id) 
    {
        
        $this->denyAccessUnlessGranted('ROLE_MANAGEMENT', null, 'Unable to access this page!');
        
        $em = $this->getDoctrine()->getManager();
        
        $fighter = $this->getDoctrine()
                        ->getRepository('AppBundle:Fighter')
                        ->findOneBy(array('id' => $id));
        
        if($fighter->getId() == $id) {
            $fighter->setDeleted(true);
            $fighter->setGroupId(0);
            $em->persist($fighter);
        }
        
        $em->flush();
        
        
        return $this->redirectToRoute('fighterIndex');
    }


}
