<?php

namespace AppBundle\Controller;

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
     * @Route("/competition_groups", name="competition_groups_view")
     */
    public function indexAction(Request $request)
    {
        
        
        // replace this example code with whatever you need
        /*return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]); */
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
               $fighterRepository = $this->getDoctrine()
                        ->getRepository('AppBundle:Fighter');
                
                $query = $fighterRepository->createQueryBuilder('f')
                            ->where('f.inFight = 0')
                            ->getQuery();
                
                $fighters = $query->getResult();
                
                foreach ($fighters as $fighter) {
                    //print_r($fighter);
                    $competitionGroupsArr[$fighter->getGroupId()][] = $fighter;
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
