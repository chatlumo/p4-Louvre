<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 21/02/2018
 * Time: 13:37
 */

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderControllerTest extends WebTestCase
{
    const TUESDAY = 2;
    const SUNDAY = 7;

    /** @var \DateTime $dateOfVisit */
    protected $dateOfVisit;

    protected function setUp()
    {

        $today = new \DateTime();
        $daysLater = 14;

        if ($today->format('N') == self::TUESDAY || $today->format('N') == self::SUNDAY) {
            $daysLater++;
        }

        $interval = new \DateInterval('P'.$daysLater.'D');

        $this->dateOfVisit = $today->add($interval);
    }

    /**
     * @dataProvider urlHomepageProvider
     */
    public function testHomepageIsUp($url, $expectedStatusCode)
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertSame($expectedStatusCode, $client->getResponse()->getStatusCode());

    }

    public function urlHomepageProvider()
    {
        return [
            ['/', 301],
            ['/fr/', 200],
            ['/en/', 200],
        ];
    }

    /**
     * @dataProvider urlStepsProvider
     */
    public function testOtherStepsRoutesAreOk($url, $expectedStatusCode)
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertSame($expectedStatusCode, $client->getResponse()->getStatusCode());

    }

    public function urlStepsProvider()
    {
        return [
            ['/fr/step2', 302],
            ['/fr/step3', 302],
            ['/fr/success', 302],
            ['/fr/contact', 200],
            ['/fr/cgv', 200],
        ];
    }

    public function testSuccessRouteIsOk()
    {
        $client = static::createClient();
        $client->request('POST', '/fr/checkout');

        $this->assertSame(302, $client->getResponse()->getStatusCode());

    }

    public function testHomepage(){
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/');

        $this->assertSame(1, $crawler->filter('html:contains("Choisir la date de visite")')->count());
    }



    public function testStep1ToStep2(){
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/');

        $form = $crawler->selectButton('Passer à l\'étape 2')->form();
        $form['order[dateOfVisit]'] = $this->dateOfVisit->format('Y-m-d');
        $form['order[fullDay]'] = 1;
        $form['order[nbTickets]'] = 1;
        $form['order[email][first]'] = 'monemail@yahoo.fr';
        $form['order[email][second]'] = 'monemail@yahoo.fr';

        $client->submit($form);

        $crawler = $client->followRedirect();

        //echo $client->getResponse()->getContent();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Titulaire du billet")')->count());
    }

    public function testStep1ToStep3(){
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/');

        $form = $crawler->selectButton('Passer à l\'étape 2')->form();
        $form['order[dateOfVisit]'] = $this->dateOfVisit->format('Y-m-d');
        $form['order[fullDay]'] = 1;
        $form['order[nbTickets]'] = 1;
        $form['order[email][first]'] = 'monemail@yahoo.fr';
        $form['order[email][second]'] = 'monemail@yahoo.fr';

        $client->submit($form);

        $crawler = $client->followRedirect();

        //echo $client->getResponse()->getContent();

        //Step2ToStep3
        $form = $crawler->selectButton('Passer à l\'étape 3')->form();
        $form['tickets_form[tickets][0][birthdate][day]'] = 25;
        $form['tickets_form[tickets][0][birthdate][month]'] = 04;
        $form['tickets_form[tickets][0][birthdate][year]'] = 1980;
        $form['tickets_form[tickets][0][lastname]'] = 'Nom';
        $form['tickets_form[tickets][0][firstname]'] = 'Prenom';
        $form['tickets_form[tickets][0][country]'] = 'FR';

        $client->submit($form);

        //echo $client->getResponse()->getContent();

        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('html:contains("Montant total")')->count());
    }

}