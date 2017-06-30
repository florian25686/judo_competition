<?php

namespace AppBundle\Controller;

use AppBundle\Form\GroupType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Groups;

/**
 * Kontroller für 5er Wettkampfgruppen
 * Dient zur Erstellung und Berechnung der Wettkampfgruppen
 *
 */
class CompetitionGroupsController extends Controller
{
    const minFightersPerGroup = 2;
    /**
     * Zeige Wettkampfgruppen an
     * @Route("/competitionGroups", name="competition_groups_view")
     */
    public function indexAction(Request $request)
    {
        $groupsRepository = $this->getDoctrine()
                                    ->getRepository('AppBundle:Groups');

        $groups = $groupsRepository->findBy(
                    array('deleted' => 0)
                );        
        
        $competitionGroupsArr = array();
        foreach ($groups as $group) {
            $fighters = $group->getFighters();
            $numberFighters = count($fighters);
            $competitionGroupsArr[$group->getAgeGroup()->getName()][$group->getId()]['numberFighters'] = $numberFighters;
            $competitionGroupsArr[$group->getAgeGroup()->getName()][$group->getId()]['status']         = $group->getStatus();
            $competitionGroupsArr[$group->getAgeGroup()->getName()][$group->getId()]['fighters']       = $fighters;
        }

        return $this->render('competitionGroups/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'competitionGroups' => $competitionGroupsArr,
        ]);
    }

    /**
     * @Route("/competitionGroups/display_group/{id}/{display}", name="competition_group_display", requirements={"id": "\d+"})
     */
    public function displayCompetitionGroups($id = -1, $display = 'page')
    {
        $em = $this->getDoctrine()->getManager();

       $groupsRepository = $this->getDoctrine()
                            ->getRepository('AppBundle:Groups');

        $groups = $groupsRepository->findBy(
                    array('id' => $id, 'deleted' => 0)
                );

        $competitionGroupsTmp = array();
        foreach ($groups as $group) {
            $fightersInGroup = $group->getFighters();

            foreach ($fightersInGroup as $fighter) {
                $competitionGroupsTmp[$group->getId()][] = $fighter;
            }
        }

        $competitionGroupFights = $this->generateFights($competitionGroupsTmp);

        if ($display == 'pdf') {
            $pdfResponse = $this->createPDFVersionOfGroup($id, $competitionGroupFights, $competitionGroupsTmp);

            $group = $groupsRepository->findOneBy(array('id'=>$id));
            $group->setStatus(2);
            $em->persist($group);
            $em->flush();

            return $pdfResponse;
        }

        return $this->render('competitionGroups/groupList.html.twig', [
            'competitionGroupsFights'  => $competitionGroupFights,
                'competitionGroups' => $competitionGroupsTmp,
           ]);
    }

    /**
     * @Route("/competitionGroups/createGroup", name="competition_group_create")
     */
    public function createGroup(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGEMENT', null, 'Unable to access this page!');
        
        $em = $this->getDoctrine()->getManager();
        
        $group = new Groups();
        $group->setStatus(1);
        $group->setDeleted(0);

        $form = $this->createForm(GroupType::class, $group);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $group = $form->getData();
            // Check if group exists


            $em->persist($group);
            $em->flush();

            return $this->redirectToRoute('competition_groups_view');
        }
        return $this->render('competitionGroups/createGroups.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/competitionGroups/delete_group/{id}", name="competition_group_delete")
     */
    public function deleteGroup($id)
    {
        $em = $this->getDoctrine()->getManager();

        $groupToDelete = $this->getDoctrine()
            ->getRepository('AppBundle:Groups')
            ->findOneBy(array('id' => $id));

        if($groupToDelete->getId() == $id)
        {
            $numberActiveFighters = count($groupToDelete->getFighters());
            if($numberActiveFighters == 0)
            {
                $groupToDelete->setDeleted(1);
                $em->persist($groupToDelete);
                $em->flush();
                $this->addFlash('success', 'message.group.deleted');
            } elseif ($numberActiveFighters > 0) {
               $this->addFlash('error', 'message.group.not_empty');
            }

        }

        return $this->redirectToRoute('competition_groups_view');
    }

    /**
     * Generate fights dynamically based on the inputs array
     *
     * @param array $competitionGroupsTmp
     * @return array
     */
    private function generateFights(array $competitionGroupsTmp)
    {
        $generatedFights = array();

        foreach ($competitionGroupsTmp as $groupNumber => $groupFighters) {
            $numberOfFighters = count($groupFighters);
            $generatedFights = $this->FightLayoutByMemberCount($numberOfFighters,$groupFighters);

        }
        return $generatedFights;
    }

    protected function createPDFVersionOfGroup($groupId, $competitionGroupsArr, $competitionGroupsTmp)
    {
        $filename = 'Gruppe_'.$groupId.'.pdf';

        $html = $this->renderView('competitionGroups/groupList_PDF.html.twig', array(
              'competitionGroupsFights'  => $competitionGroupsArr,
              'competitionGroups' => $competitionGroupsTmp,
      ));

        $snappy = $this->get('knp_snappy.pdf');
        $snappy->setOption('page-size', 'A4');
        $snappy->setOption('orientation', 'Portrait');

        return new Response(
          $snappy->getOutputFromHtml($html),
          200,
          array(
              'Content-Type'          => 'application/pdf',
              'Content-Disposition'   => 'attachment; filename="'.$filename.'"'
          )
      );
    }
    
    private function FightLayoutByMemberCount($numberFighters, $groupFighters) 
    {
        $generatedFights = array();
        switch ($numberFighters):
            case 2:
                    $generatedFights[0]['white'] = $groupFighters[0];
                    $generatedFights[0]['blue']  = $groupFighters[1];
                    $generatedFights[1]['white'] = $groupFighters[1];
                    $generatedFights[1]['blue']  = $groupFighters[0];
                    $generatedFights[2]['white'] = $groupFighters[0];
                    $generatedFights[2]['blue']  = $groupFighters[1];
                    break;
            case 3: 
                    $generatedFights[0]['white'] = $groupFighters[0];
                    $generatedFights[0]['blue']  = $groupFighters[1];
                    $generatedFights[1]['white'] = $groupFighters[0];
                    $generatedFights[1]['blue']  = $groupFighters[2];
                    $generatedFights[2]['white'] = $groupFighters[1];
                    $generatedFights[2]['blue']  = $groupFighters[2];
                   break;
            case 4: 
                    $generatedFights[0]['white'] = $groupFighters[0];
                    $generatedFights[0]['blue']  = $groupFighters[1];
                    $generatedFights[1]['white'] = $groupFighters[2];
                    $generatedFights[1]['blue']  = $groupFighters[3];
                    $generatedFights[3]['white'] = $groupFighters[0];
                    $generatedFights[3]['blue']  = $groupFighters[2];
                    $generatedFights[4]['white'] = $groupFighters[1];
                    $generatedFights[4]['blue']  = $groupFighters[3];
                    $generatedFights[6]['white'] = $groupFighters[1];
                    $generatedFights[6]['blue']  = $groupFighters[2];
                    $generatedFights[7]['white'] = $groupFighters[0];
                    $generatedFights[7]['blue']  = $groupFighters[3];
                   break;
            case 5:
                    $generatedFights[0]['white'] = $groupFighters[0];
                    $generatedFights[0]['blue']  = $groupFighters[1];
                    $generatedFights[1]['white'] = $groupFighters[2];
                    $generatedFights[1]['blue']  = $groupFighters[3];
                    $generatedFights[2]['white'] = $groupFighters[0];
                    $generatedFights[2]['blue']  = $groupFighters[4];
                    $generatedFights[3]['white'] = $groupFighters[1];
                    $generatedFights[3]['blue']  = $groupFighters[3];
                    $generatedFights[4]['white'] = $groupFighters[0];
                    $generatedFights[4]['blue']  = $groupFighters[2];
                    $generatedFights[5]['white'] = $groupFighters[1];
                    $generatedFights[5]['blue']  = $groupFighters[4];
                    $generatedFights[6]['white'] = $groupFighters[0];
                    $generatedFights[6]['blue']  = $groupFighters[3];
                    $generatedFights[7]['white'] = $groupFighters[2];
                    $generatedFights[7]['blue']  = $groupFighters[4];
                    $generatedFights[8]['white'] = $groupFighters[1];
                    $generatedFights[8]['blue']  = $groupFighters[2];
                    $generatedFights[9]['white'] = $groupFighters[3];
                    $generatedFights[9]['blue']  = $groupFighters[4];
                break;
            case 6:
                    // 1
                    $generatedFights[0]['white'] = $groupFighters[0];
                    $generatedFights[0]['blue']  = $groupFighters[1];
                    // 2
                    $generatedFights[1]['white'] = $groupFighters[2];
                    $generatedFights[1]['blue']  = $groupFighters[3];
                    //3
                    $generatedFights[2]['white'] = $groupFighters[4];
                    $generatedFights[2]['blue']  = $groupFighters[5];
                    //4
                    $generatedFights[3]['white'] = $groupFighters[0];
                    $generatedFights[3]['blue']  = $groupFighters[2];
                    //5
                    $generatedFights[4]['white'] = $groupFighters[1];
                    $generatedFights[4]['blue']  = $groupFighters[3];
                    //6
                    $generatedFights[5]['white'] = $groupFighters[0];
                    $generatedFights[5]['blue']  = $groupFighters[4];
                    //7
                    $generatedFights[6]['white'] = $groupFighters[2];
                    $generatedFights[6]['blue']  = $groupFighters[5];
                    //8
                    $generatedFights[7]['white'] = $groupFighters[0];
                    $generatedFights[7]['blue']  = $groupFighters[3];
                    //9
                    $generatedFights[8]['white'] = $groupFighters[1];
                    $generatedFights[8]['blue']  = $groupFighters[5];
                    //10
                    $generatedFights[9]['white'] = $groupFighters[3];
                    $generatedFights[9]['blue']  = $groupFighters[4];
                    //11
                    $generatedFights[10]['white'] = $groupFighters[1];
                    $generatedFights[10]['blue']  = $groupFighters[2];
                    //12
                    $generatedFights[11]['white'] = $groupFighters[3];
                    $generatedFights[11]['blue']  = $groupFighters[5];
                    //13
                    $generatedFights[12]['white'] = $groupFighters[1];
                    $generatedFights[12]['blue']  = $groupFighters[4];
                    //14
                    $generatedFights[13]['white'] = $groupFighters[0];
                    $generatedFights[13]['blue']  = $groupFighters[5];
                    //15
                    $generatedFights[14]['white'] = $groupFighters[2];
                    $generatedFights[14]['blue']  = $groupFighters[4];
                break;
            default:
                $generatedFights = array();
                break;
        endswitch;
        
        return $generatedFights;
        
    }
    
    
    /**
     * This function returns the value of group open State
     */
    protected function getOpenGroupState()
    {
        return 1;
    }

    protected function minFightersPerGroup()
    {
        return 2;
    }


}
