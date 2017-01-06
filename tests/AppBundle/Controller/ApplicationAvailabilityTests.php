<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        return array(
            array('/'),
            array('/fighter'),
            array('/fighter/createFighter'),
            array('/fighter/editFighter/2'),
            array('/competitionGroups'),
            array('/competitionGroups/display_group/2/page'),
            array('/competitionGroups/display_group/2/pdf'),
        );
    }
}
