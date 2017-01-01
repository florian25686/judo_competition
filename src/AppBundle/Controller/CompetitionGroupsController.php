<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        $groups = $groupsRepository->findAll(
                    array('deleted' => null)
                );

        $fighterRepository = $this->getDoctrine()
                ->getRepository('AppBundle:Fighter');

        $competitionGroupsArr = '';
        foreach ($groups as $group) {
            $fighters = $fighterRepository
                        ->findBy(
                            array('groupId' => $group->getId())
                        );
            $numberFighters = count($fighters);

            $competitionGroupsArr[$group->getId()]['numberFighters'] = $numberFighters;
            $competitionGroupsArr[$group->getId()]['status']         = $group->getStatus();
            $competitionGroupsArr[$group->getId()]['fighters']       = $fighters;
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
                    array('id' => $id, 'deleted' => null)
                );

        $fighterRepository = $this->getDoctrine()
                ->getRepository('AppBundle:Fighter');

        $competitionGroupsTmp = '';
        foreach ($groups as $group) {
            $fightersInGroup = $fighterRepository
                        ->findBy(
                            array('groupId' => $group->getId())
                        );

            foreach ($fightersInGroup as $fighter) {
                $competitionGroupsTmp[$group->getId()][] = $fighter;
            }
        }

        // TODO: Function generate Fights must be refactored
        $competitionGroupsArr = $this->generateFights($competitionGroupsTmp);

        if ($display == 'pdf') {
            $pdfResponse = $this->createPDFVersionOfGroup($id, $competitionGroupsArr, $competitionGroupsTmp);
            return $pdfResponse;
        }

        return $this->render('competitionGroups/groupList.html.twig', [
            'competitionGroupsFights'  => $competitionGroupsArr,
                'competitionGroups' => $competitionGroupsTmp,
           ]);
    }

    /**
     * TODO: This function should be excluded into another controller
     * Generate fights dynamically based on the inputs array
     *
     * @param array $competitionGroupsTmp
     * @return array
     */
    private function generateFights(array $competitionGroupsTmpArr)
    {
        $competitionGroupsArr = array();

        // There might be more than one group, deal with it
        foreach ($competitionGroupsTmpArr as $key => $competitionGroupsTmp) {
            $anzahl_fighter = count($competitionGroupsTmp);
            $x = 0; // Counter Fighter
            $i = 0; // Counter Fight
            while ($x < $anzahl_fighter-1) {
                $y = 0;
                while ($y < $anzahl_fighter) {

                    // Check if fighter does fight vs himself
                    if ($competitionGroupsTmp[$x] !== $competitionGroupsTmp[$y]) {
                        if ($x < $y) {
                            if ($competitionGroupsTmp[$y] === $competitionGroupsTmp[$y-1]
                                ) {
                                $competitionGroupsArr[$i]['white'] = $competitionGroupsTmp[$x];
                                $competitionGroupsArr[$i]['blue']  = $competitionGroupsTmp[$y];
                            }

                            // turn the sides for the fighter
                            $x_lines = $x + $y % 2;
                            if ($x_lines == 0) {
                                $competitionGroupsArr[$i]['white'] = $competitionGroupsTmp[$x];
                                $competitionGroupsArr[$i]['blue']  = $competitionGroupsTmp[$y];
                            } else {
                                $competitionGroupsArr[$i]['white'] = $competitionGroupsTmp[$y];
                                $competitionGroupsArr[$i]['blue']  = $competitionGroupsTmp[$x];
                            }
                        }
                        $y++;
                    }
                    // Fighter already in the list on the opposite site or would fight himself
                    // Jump over it
                    elseif ($competitionGroupsTmp[$x] === $competitionGroupsTmp[$y]) {
                        if ($y+1 == $x && $y+1 <= $anzahl_fighter) {
                            $y+2;
                        } else {
                            $y++;
                        }
                       // $i--;
                    }
                    $i++;
                }
                // $i++;
                $x++;
            }
        }

        return $this->validateFightOrder($competitionGroupsArr);
         //  $competitionGroupsArr;
    }

    private function validateFightOrder($competitionGroupArr)
    {
        $orderedCompetitionGroup = array();
        $fightNumbers = array_keys($competitionGroupArr);
        $fightNumber = 0;
        foreach ($competitionGroupArr as $fight) {
            $fighterWhiteID = $fight['white']->getId();
            $fighterBlueID  = $fight['blue']->getId();
            $fighterBefore = array();
            if ($fightNumber >= 1) {
                $fightNumberBefore = $fightNumber-1;

                $fighterBefore[$competitionGroupArr[ $fightNumbers[$fightNumberBefore] ]['white']->getId()] = 1;
                $fighterBefore[$competitionGroupArr[ $fightNumbers[$fightNumberBefore] ]['blue']->getId()] = 1;

          /*
          print "<hr>";
          print_r($fighterBefore);
          print "figher White ID :".$fighterWhiteID."\r\n";
          print "fighter Blue ID: ".$fighterBlueID."\r\n";
          */
          // One of the fighters exists in the fight before,
          // add one to the key and return back to normal count
          if (array_key_exists($fighterWhiteID, $fighterBefore) || array_key_exists($fighterBlueID, $fighterBefore)) {
              $newPosition = $this->findNewKey($orderedCompetitionGroup, count($orderedCompetitionGroup)+2);

              $orderedCompetitionGroup[$newPosition]['white'] = $fight['white'];
              $orderedCompetitionGroup[$newPosition]['blue'] = $fight['blue'];
          } else {
              $orderedCompetitionGroup[$fightNumber]['white'] = $fight['white'];
              $orderedCompetitionGroup[$fightNumber]['blue'] = $fight['blue'];
              $fightNumber++;
          }
            } else {
                $orderedCompetitionGroup[$fightNumber]['white'] = $fight['white'];
                $orderedCompetitionGroup[$fightNumber]['blue'] = $fight['blue'];
          // $fightNumber++;
            }
            $fightNumber++;
        } // close foreach

      return $orderedCompetitionGroup;
    } // close function

    protected function findNewKey($array, $desiredKey)
    {
        $freePosition = $desiredKey;
        while (array_key_exists($freePosition, $array)) {
            $freePosition+2;
        }
        return $freePosition;
    }

    protected function createPDFVersionOfGroup($groupId, $competitionGroupsArr, $competitionGroupsTmp)
    {
        $filename = 'Group_'.$groupId.'.pdf';

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
