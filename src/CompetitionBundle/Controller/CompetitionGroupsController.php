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
            $competitionGroupsArr[$group->getId()]['fighters']        = $fighters;
        }
        
        // replace this example code with whatever you need
        return $this->render('competitionGroups/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'competitionGroups' => $competitionGroupsArr,
        ]);
    }
    
    /**
     * @Route("/competition_groups/generate_groups", name="competition_groups_create")
     */
    public function createCompetitionGroups(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        /**
         * dry-run, all, gruppennummer für neuerstellung
         */
        $method = $request->attributes->get('method');
        $competitionGroupsArr = array();
 
        switch($method) {
            case 'all':
            case 'dry-run':
            case 'gruppennummer':
                break;
               
            default:
                $groupsRepository = $this->getDoctrine()
                                    ->getRepository('CompetitionBundle:Groups');
                
                $groups = $groupsRepository->findAll(
                            array('status' => 1, 'deleted' => NULL)
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
                break;
        }

        return $this->render('competitionGroups/groupList.html.twig', [
        	'competitionGroups' => $competitionGroupsArr,
	]);


print "<pre>";
print_r($competitionGroupsArr);
print "</pre>";
return null;
        
    }
}
