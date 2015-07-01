<?php

namespace Houdtbaar\HangmanBundle;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;


class WordsRepositoryTest extends KernelTestCase
{
    private $em;

    /**
     * Setup bootstrapping and setup entitymanager
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Loads doctrine fixtures for test isolation
     * @throws \Exception
     */
    public static function setUpBeforeClass()
    {
        $kernel = new \AppKernel('test', false);
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
     * Test the random function
     * @todo find a way to test randomness of this function, only way now is; test a large number of times after each other
     */

    public function testFindRandomWithDb()
    {

        $word = $this->em
            ->getRepository('HoudtbaarHangmanBundle:Word')
            ->findRandom();

        $this->assertInternalType('object', $word);
        $this->assertNotEmpty($word);
    }


    /**
     * Test getting a random list of items
     */
    public function testGetRandomList()
    {
        $limit = 10;

        $randomList = $this->em
            ->getRepository('HoudtbaarHangmanBundle:Word')
            ->getRandomList($limit);

        $this->assertInternalType('array', $randomList);
        $this->assertNotSame(0, $randomList);
        $this->assertSame($limit, count($randomList));
    }

    /**
     * Check if the array has duplicates, to check this we need a large list
     */
    public function testGetRandomListNoDublicates()
    {

        $limit = 100;

        $randomList = $this->em
            ->getRepository('HoudtbaarHangmanBundle:Word')
            ->getRandomList($limit);

        $uniqueRandomList = array_unique($randomList);

        $this->assertInternalType('array', $randomList);
        $this->assertSame(count($uniqueRandomList), count($randomList));
        $this->assertSame($uniqueRandomList, $randomList);
    }


    /**
     * Helper function to get data for a game
     * @return array
     */
    public function getGameTestData()
    {
        return array(
            array(
                'id' => 1,
                'word' => 'a..a..a.....',
                'status' => 'busy',
                'tries_left' => 7
            ),
            array(
                'id' => 2,
                'word' => 'ab',
                'status' => 'succes',
                'tries_left' => 7
            ),
            array(
                'id' => 3,
                'word' => '.........',
                'status' => 'busy',
                'tries_left' => 7,
            )
        );
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
