<?php

namespace Houdtbaar\HangmanBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpFoundation\Response;

class GameControllerWebTest extends WebTestCase
{
    private $client;

    /**
     * @throws \Exception
     */
    protected function setUp()
    {
        parent::setUp();
        $kernel = static::createKernel();
        $kernel->boot();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $options['-e'] = 'test';
        $options['-q'] = null;
        $options['-n'] = true;
        $options['--purge-with-truncate'] = null;
        $options['--fixtures'] = __DIR__.'/../../DataFixtures/ORM/';

        $input = new ArrayInput(
            array_merge($options, array('command' => 'doctrine:fixtures:load'))
        );

        $result = $application->run($input);
    }

    /**
     * test if game response route is working
     */
    public function testGameResponseAction()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/games/1',
            array(),
            array(),
            array(),
            ''
        );

        $this->assertSame(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );

        $this->assertSame(
            '{"status":200,"data":{"id":1,"word":"a..a..a.....","status":"busy","tries_left":7}}',
            $client->getResponse()->getContent()
        );
    }

    /**
     * test if guessing route is working
     */
    public function testGameGuessAction()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/games/1',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"char":"A"}'
        );

        $this->assertSame(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );

        $this->assertSame(
            '{"status":200,"data":{"id":1,"word":"a..a..a.....","status":"busy","tries_left":7}}',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Test if input is validated by controller, if the input is not more than one character
     */
    public function testGameGuessActionInvalidLetters()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/games/1',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"char":"FF"}'
        );

        $this->assertSame(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $client->getResponse()->getStatusCode()
        );
        $this->assertSame(
            '{"status":422,"data":{"response":"Dit zijn meerdere letters, meerdere letters tegelijk proberen is niet toegestaan.","char":"FF"}}',
            $client->getResponse()->getContent()
        );

    }

    /**
     *  Test if input is validated by controller, if the input is not a valid character, a number
     */
    public function testGameGuessActionDigitInSteadOfLetters()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/games/1',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"char":"1"}'
        );

        $this->assertSame(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $client->getResponse()->getStatusCode()
        );
        $this->assertSame(
            '{"status":422,"data":{"response":"Dit is geen letter.","char":"1"}}',
            $client->getResponse()->getContent()
        );
    }

    /**
     *  Test if input is validated by controller, if the input is not a valid character, no input
     */
    public function testGameGuessActionNoLettersAtAll()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/games/1',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"char":""}'
        );

        $this->assertSame(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $client->getResponse()->getStatusCode()
        );
        $this->assertSame(
            '{"status":422,"data":{"response":"Dit is geen letter.","char":""}}',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Test if new game route is working
     */
    public function testGameNewAction()
    {

        $client = static::createClient();
        $client->request('POST', '/games');

        $this->assertSame(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );
        $this->assertSame(
            '{"status":200,"data":4}',
            $client->getResponse()->getContent()
        );
    }

    /**
     * test if the Games overview page is well routed
     */
    public function testGameOverviewAction()
    {
        $client = static::createClient();
        $client->request('GET', '/games');

        $this->assertSame(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );
        $this->assertSame(
            '{"status":200,"data":[{"id":1,"word":"a..a..a.....","status":"busy","tries_left":7},{"id":2,"word":"ab","status":"succes","tries_left":7},{"id":3,"word":".........","status":"busy","tries_left":7}]}',
            $client->getResponse()->getContent()
        );
    }


    /**
     * guessing the whole word 'aardvarks'
     */
    public function testGameGuessActionGuessingWholeWord()
    {

        $letters = array('a', 'r', 'd', 'v', 'k', 's',);

        foreach ($letters as $letter) {

            $client = static::createClient();
            $client->request(
                'POST',
                '/games/3',
                array(),
                array(),
                array('CONTENT_TYPE' => 'application/json'),
                json_encode(array('char' => $letter))
            );
        }

        $client = static::createClient();
        $client->request(
            'GET',
            '/games/3',
            array(),
            array(),
            array(),
            ''
        );

        $this->assertContains(
            'aardvarks',
            $client->getResponse()->getContent()
        );

        $this->assertSame(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );
    }


    protected function tearDown()
    {
        $this->client = null;
    }

}
