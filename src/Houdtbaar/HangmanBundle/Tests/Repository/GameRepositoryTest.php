<?php

namespace Houdtbaar\HangmanBundle;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Houdtbaar\HangmanBundle\Entity\Game;

class GameRepositoryTest extends KernelTestCase
{

    private $em;

    /**
     * Setup bootstrapping and setup entitymanager
     * Loads doctrine fixtures for test isolation
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

        $application->run($input);
    }

    /**
     * Finds game by Id and return the game as an array
     */
    public function testFindGameById()
    {

        $games = $this->getGameTestDataArray();

        // real repository
        $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        );

        $this->assertSame(
            $gameRepository->findGameById(1),
            $games[0]
        );

    }

    /**
     * Finds game by Id if game is not know return an empty array
     */
    public function testFindGameByIdNotKnow()
    {
        // real repository
        $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        );

        $this->assertSame(
            $gameRepository->findGameById(4),
            array()
        );

    }

    /**
     * test if array of games is returned
     */
    public function testGetGamesOverview()
    {

        $gamesObjects = $this->getGameTestDataObjects();

        // real repository
        $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        );

        $response = $gameRepository->getGamesOverview($gamesObjects);

        $this->assertSame(
            count($response),
            3
        );
        $this->assertInternalType('array', $response[0]);
    }

    /**
     * adds a game to the database. Expects a new word as an argument
     */
    public function testAddGame()
    {
        $word = 'aardvarks';

        // real repository
        $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        );

        $newGameId = $gameRepository->addGame($word);
        $newGame = $gameRepository->find($newGameId);

        $this->assertSame($newGame->getWord(), $word);
    }

    /**
     *  update the game in the database, function save the entity to the database
     */
    public function testUpdateGame()
    {
        // real repository
        $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        );

        $gameObject = $gameRepository->findOneBy(array('id' => 1));

        $gameObject->addTriedletters('v');
        $gameRepository->saveGame($gameObject);


        $this->assertContains('v', $gameObject->getTriedletters());
    }

    /**
     * Test get list of games with a limit of 10, we expect only 4 returned
     */
    public function testGetGames()
    {
        $limit = 10;

        // real repository
        $repsonse = $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        )->GetGames($limit);


        $this->assertCount(4, $repsonse);
        $this->assertInternalType('object', $repsonse[0]);
    }

    /**
     * Test get list of games when no limit is given
     */
    public function testGetGamesNoLimit()
    {
        // real repository
        $repsonse = $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        )->GetGames();


        $this->assertCount(4, $repsonse);
        $this->assertInternalType('object', $repsonse[0]);
    }

    /**
     * Test get list of games when a low is given
     */
    public function testGetGamesOnlyTwoLimit()
    {
        $limit = 2;

        // real repository
        $repsonse = $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        )->GetGames($limit);


        $this->assertCount(2, $repsonse);
        $this->assertInternalType('object', $repsonse[0]);
    }

    /**
     * state when a correct letter is guessed
     */
    public function testCheckCorrectLetter()
    {
        $char = 'a';
        $id = 1; // word: advantageous

        // get game object
        $game = $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        )->findOneBy(array('id' => $id));

        // real repository
        $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        );

        $repsone = $gameRepository->checkLetter($char, $game);

        // check game object state

        $this->assertContains($char, $game->getTriedletters());
        $this->assertContains($char, $game->getGuessedletters());
    }


    /**
     * return null if a digit is used to guess, expects number of tries to stay the same
     */
    public function testCheckLetterWithDigit()
    {
        $char = '2';
        $id = 1; // word: advantageous

        // get game object
        $game = $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        )->findOneBy(array('id' => $id));

        // real repository
        $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        );

        $respone = $gameRepository->checkLetter($char, $game);

        // check game object state

        $this->assertSame(null, $respone);
        $this->assertNotContains($char, $game->getGuessedletters());
    }

    /**
     * state if letter is not guessed
     */
    public function testCheckIncorrectLetter()
    {
        $char = 'q';
        $id = 1; // word: advantageous

        // get game object
        $game = $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        )->findOneBy(array('id' => $id));

        // real repository
        $gameRepository = $this->em->getRepository(
            'HoudtbaarHangmanBundle:Game'
        );

        $repsone = $gameRepository->checkLetter($char, $game);

        // check game object state

        $this->assertContains($char, $game->getTriedletters());
        $this->assertNotContains($char, $game->getGuessedletters());
    }


    /**
     * Helper function to get data for a game
     * @return array
     */
    public function getGameTestDataArray()
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

    /**
     * Returns array of game objects to help with testing
     * @return array
     */
    public function getGameTestDataObjects()
    {

        $item1 = new Game();
        $item1->setId(1);
        $item1->setWord("advantageous");
        $item1->setGuessedLetters(unserialize('a:1:{i:0;s:1:"a";}'));
        $item1->setTriedLetters(unserialize('a:1:{i:0;s:1:"a";}'));

        $item2 = new Game();
        $item2->setId(2);
        $item2->setWord("ab");
        $item2->setGuessedLetters(
            unserialize('a:2:{i:0;s:1:"a";i:1;s:1:"b";}')
        );
        $item2->setTriedLetters(unserialize('a:2:{i:0;s:1:"a";i:1;s:1:"b";}'));

        $item3 = new Game();
        $item3->setId(3);
        $item3->setWord("aardvarks");
        $item3->setGuessedLetters(unserialize('a:0:{}'));
        $item3->setTriedLetters(unserialize('a:0:{}'));

        return array($item1, $item2, $item3);

    }

    /**
     * Teardown function
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
