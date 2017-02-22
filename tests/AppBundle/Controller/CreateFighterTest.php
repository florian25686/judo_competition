<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateFighterFunctionalTest extends WebTestCase
{

    public function testCreateFighter()
    {
	$fighterPersonInformation = $this->fighterCreateData();
        $client = self::createClient();
	
	
        $client->request('POST', '/fighter/createFighter', $fighterPersonInformation);
	
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    private function fighterCreateData() {

    	    $fighterCreateData = array('lastName' => 'Völker',
				 'firstName' => 'Florian',
				 'club' => 'UnitTest',
				 'gender' => 'm',
				 'weight' => '32.2',
				 'groupId' => '200'
				 );
	return $fighterCreateData;
    
    }

}
