<?php

namespace CompetitionBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Kontroller für 5er Wettkampfgruppen
 * Dient zur Erstellung und Berechnung der Wettkampfgruppen
 * 
 */
class CompetitionGroupsController extends Controller
{
    /**
     * Zeige Wettkampfgruppen an
     * @Route("/competitionGroups", name="competition_groups_view")
     */
    public function indexAction(Request $request)
    {
        $groupsRepository = $this->getDoctrine()
                                    ->getRepository('CompetitionBundle:Groups');
                
        $groups = $groupsRepository->findAll(
                    array('deleted' => NULL)
                );

        $fighterRepository = $this->getDoctrine()
                ->getRepository('CompetitionBundle:Fighter');

        $competitionGroupsArr = '';
        foreach ($groups as $group) 
        {                    
            $fighters = $fighterRepository
                        ->findBy(
                            array('groupId' => $group->getId())
                        );
            $numberFighters = count($fighters);
            
            $competitionGroupsArr[$group->getId()]['numberFighters'] = $numberFighters;
            $competitionGroupsArr[$group->getId()]['status']         = $group->getStatus();
            $competitionGroupsArr[$group->getId()]['fighters']       = $fighters;
        }
        
        // replace this example code with whatever you need
        return $this->render('competitionGroups/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'competitionGroups' => $competitionGroupsArr,
        ]);
    }
    
    /**
     * @Route("/competitionGroups/generate_group/{id}", name="competition_group_create", requirements={"id": "\d+"})
     */
    public function createCompetitionGroups($id = -1)
    {
        $em = $this->getDoctrine()->getManager();

        $groupsRepository = $this->getDoctrine()
                            ->getRepository('CompetitionBundle:Groups');

        $groups = $groupsRepository->findBy(
                    array('id' => $id, 'status' => 1, 'deleted' => NULL)
                );

        $fighterRepository = $this->getDoctrine()
                ->getRepository('CompetitionBundle:Fighter');

        $competitionGroupsArr = '';
        foreach ($groups as $group) 
        {                    
            $fighters = $fighterRepository
                        ->findBy(
                            array('groupId' => $group->getId())
                        );
            
            if (count($fighters) >= 2) 
            {
                foreach ($fighters as $fighter) 
                {
                    $fighter->setInFight(TRUE);
                    $em->persist($fighter);
                    $competitionGroupsArr[$group->getId()][] = $fighter;
                }
                $group->setStatus(2);
            }
            else {
                foreach ($fighters as $fighter) 
                {
                    $competitionGroupsArr[$group->getId()][] = $fighter;
                }
            }
        }

        $em->flush();
           

        return $this->render('competitionGroups/groupList.html.twig', [
        	'competitionGroups' => $competitionGroupsArr,
	]);        
    }
    
    /**
     * @Route("/competitionGroups/display_group/{id}", name="competition_group_display", requirements={"id": "\d+"})
     */
    public function displayCompetitionGroups($id = -1)
    {
        $em = $this->getDoctrine()->getManager();

        $groupsRepository = $this->getDoctrine()
                            ->getRepository('CompetitionBundle:Groups');

        $groups = $groupsRepository->findBy(
                    array('id' => $id, 'deleted' => NULL)
                );

        $fighterRepository = $this->getDoctrine()
                ->getRepository('CompetitionBundle:Fighter');

        $competitionGroupsArr = '';
        foreach ($groups as $group) 
        {                    
            $fighters = $fighterRepository
                        ->findBy(
                            array('groupId' => $group->getId())
                        );
            
            foreach ($fighters as $fighter) 
            {
                $competitionGroupsArr[$group->getId()][] = $fighter;
            }
        }

        
        return $this->render('competitionGroups/groupList.html.twig', [
        	'competitionGroups' => $competitionGroupsArr,
	]);        
    }
}
