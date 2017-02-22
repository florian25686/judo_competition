<?php

namespace ImportBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/importerStart")
     */
    public function index2Action()
    {
        $importer = $this->get('importer.csv');
        $importer->setHeader(1);
        $importer->startImport('/home/florian/development/judo_competition/src/ImportBundle/Controller/fighter.csv');

        $content = $importer->getContent();
        print_r($importer->getContent());



        return $this->render('ImportBundle:Default:index.html.twig');
    }
}
