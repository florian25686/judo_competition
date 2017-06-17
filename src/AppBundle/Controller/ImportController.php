<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Fighter;
use AppBundle\Entity\Groups;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller für die Erstellung und Verwaltung von Wettkämpfern
 *
 */
class ImportController extends Controller
{
    /**
     * @Route("/importStart", name="importStart")
     */
    public function indexAction(Request $request)
    {
      $importer = $this->get('importer.csv');
      $importer->setHeader(1);
      $importer->startImport('/home/florian/development/judo_competition/src/ImportBundle/Controller/fighter.csv');

      $importedFighterFromFile = $importer->getContent();
      foreach($importedFighterFromFile as $fighterFromFile) {
        $this->createFighterAction($fighterFromFile);
      }

        return $this->render('base.html.twig');
    }

    private function createFighterAction(array $fighterFromFile)
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->fighterAlreadyExists($fighterFromFile)) {
            return;
        }

        $fighter = new Fighter();
        $fighter->setFirstName($fighterFromFile['Vorname']);
        $fighter->setLastName($fighterFromFile['Nachname']);

        $fighter->setBirthDate($fighterFromFile['Jahrgang']);
        
        $ageGroupRepository = $this->getDoctrine()
                            ->getRepository('AppBundle:AgeGroups');
        
        $ageGroup = $ageGroupRepository
                ->findBy(
                        array('name' => $fighterFromFile['Altersklasse'])
                        );
        
        $fighter->setAgeGroup($ageGroup[0]);
        $fighter->setWeight($fighterFromFile['Gewicht']);
        $fighter->setGender($fighterFromFile['Geschlecht']);
        $fighter->setClub($fighterFromFile['Verein']);
        $fighter->setGroups(null);
        $fighter->setInFight(false);
        $em->persist($fighter);
        $em->flush();

    }

    private function fighterAlreadyExists($fighterFromFile)
    {
        $fighterRepository = $fighterRepository = $this->getDoctrine()
            ->getRepository('AppBundle:Fighter');

        $ageGroupRepository = $this->getDoctrine()
            ->getRepository('AppBundle:AgeGroups');

        $ageGroup = $ageGroupRepository
            ->findBy(
                array('name' => $fighterFromFile['Altersklasse'])
            );

        $fighter = $fighterRepository->findOneBy(
            [
             'firstName' => $fighterFromFile['Vorname'],
             'lastName' => $fighterFromFile['Nachname'],
             'ageGroup' => $ageGroup[0],
            ]
        );

        if($fighter) {
            return true;
        }
    }

}
