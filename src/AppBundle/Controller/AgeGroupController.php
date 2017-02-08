<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AgeGroups;
use AppBundle\Entity\Groups;
use AppBundle\Form\FighterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AgeGroupController extends Controller
{
    /**
     * @Route("/agegroup", name="ageGroupIndex")
     */
    public function indexAction(Request $request)
    {
        $ageGroupRepository = $this->getDoctrine()
                        ->getRepository('AppBundle:AgeGroups');
        
        $ageGroups = $ageGroupRepository
                ->findBy(
                        array('deleted' => NULL)
                        );
        
        return $this->render('ageGroups/index.html.twig', [
            'ageGroups' => $ageGroups,
            
        ]);
    }

    
    private function loadAllFightersFromRepository()
    {

        $fighterRepository = $this->getDoctrine()
                        ->getRepository('AppBundle:Fighter');

        $query = $fighterRepository->createQueryBuilder('f')
                            ->where('f.deleted = 0')
                            ->orderBy('f.weight', 'ASC')
                            ->getQuery();

        $fighters = $query->getResult();

        return $fighters;
    }
    /**
     * TODO: Create the form, block the groupaddition if someone is already in a group
     *
     * @Route("/agegroup/createAgeGroup", name="createAgeGroup")
     * @param Request $request
     */
    public function createAgeGroupAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $ageGroup = new AgeGroups();
        
        $form = $this->createFormBuilder($ageGroup)
                ->add('name')
                ->add('save', SubmitType::class, array('label' => 'Save'))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $ageGroup = $form->getData();
            
            $em->persist($ageGroup);
            $em->flush();

            return $this->redirectToRoute('ageGroupIndex');
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


        $form = $this->createForm(FighterType::class, $fighter)
            ->add('weight', null, array(
                'disabled' => $disabled_fields,
             ))
            ->add('gender', null, array(
                'disabled' => $disabled_fields,
            ))
            ->add('birthDate', null, array(
                'disabled' => $disabled_fields,
            ))
            ->add('groups', null, array(
                'disabled' => $disabled_fields
            ));


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $fighter = $form->getData();
            // Check if group exists

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
    public function deleteFighter($id) {
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
        
        
        return $this->setRoute('fighterIndex');
    }


}
