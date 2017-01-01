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
        $importer->startImport('/home/florian/development/preperation/preperations/src/ImportBundle/Controller/fighter.csv');
      
        $content = $importer->getContent();
        print_r($importer->getContent());
      
      
        $mailer = $this->get('app.mailer');
        $sendStatus = $mailer->sendAction('florian@test.com', 'Hello Mails');
      
        print "sendstatus:".$sendStatus."<br>";
        return $this->render('ImportBundle:Default:index.html.twig');
    }
}
