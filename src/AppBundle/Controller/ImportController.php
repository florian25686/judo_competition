<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Fighter;
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
return "imported";
    }

    private function createFighterAction(array $fighterFromFile)
    {
        $em = $this->getDoctrine()->getManager();
        print_r($fighterFromFile);
        
        $fighter = new Fighter();
        $fighter->setFirstName($fighterFromFile['Vorname']);
        $fighter->setLastName($fighterFromFile['Nachname']);
        
        $birthDate = new \DateTime($fighterFromFile['Jahr'].'-01-01');
        $fighter->setBirthDate($birthDate);
        
        $ageGroupRepository = $this->getDoctrine()
                            ->getRepository('AppBundle:AgeGroups');
        
        $ageGroup = $ageGroupRepository
                ->findBy(
                        array('name' => $fighterFromFile['Klasse'])
                        );
        
        $fighter->setAgeGroup($ageGroup[0]);
        $fighter->setWeight($fighterFromFile['Gewicht']);
        $fighter->setGender($fighterFromFile['Geschlecht']);
        $fighter->setClub($fighterFromFile['Verein']);
        $fighter->setinFight(0);
        $fighter->setGroupId(100);

        $em->persist($fighter);
        $em->flush();

return "ok";
    }

}
