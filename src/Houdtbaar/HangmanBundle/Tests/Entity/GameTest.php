<?php

namespace Houdtbaar\HangmanBundle\Tests\Entity;

use Houdtbaar\HangmanBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class GameTest extends KernelTestCase
{

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

        $application->run($input);
    }

    /**
     *
     */
    public function testSetDottedWord()
    {

        $game = new Game();
        $game->setWord('aardvarks');
        $game->addGuessedLetters('a');
        $game->setDottedWord();

        $this->assertSame($game->getDottedWord(), 'aa...a...');
    }

    public function testSetDottedWordFail()
    {
        // original
        $game = new Game();
        $game->setWord('aardvarks');
        $game->addGuessedLetters('1');
        $game->setDottedWord();

        $this->assertSame($game->getDottedWord(), '.........');
    }


    public function testSetTriesLeft()
    {
        $game = new Game();
        $game->setWord('aardvarks');
        $game->addGuessedLetters('a');
        $game->addTriedLetters('q');
        $game->addTriedLetters('w');
        $game->addTriedLetters('t');
        $game->addTriedLetters('y');
        $game->addTriedLetters('o');
        $game->addTriedLetters('p');
        $game->addTriedLetters('f');

        $game->setTriesLeft();

        $this->assertSame($game->getTriesLeft(), 0);
    }


    public function setStatusBusy()
    {

        $game = new Game();
        $game->setWord('aardvarks');
        $game->addGuessedLetters('a');
        $game->addTriedLetters('q');
        $game->addTriedLetters('w');
        $game->addTriedLetters('t');

        $game->setTriesLeft();
        $game->setDottedWord();
        $game->setStatus();

        $this->assertSame($game->getStatus(), 'busy');
    }


    public function testSetStatusFail()
    {
        $game = new Game();
        $game->setWord('aardvarks');
        $game->addGuessedLetters('a');
        $game->addTriedLetters('q');
        $game->addTriedLetters('w');
        $game->addTriedLetters('t');
        $game->addTriedLetters('y');
        $game->addTriedLetters('o');
        $game->addTriedLetters('p');
        $game->addTriedLetters('f');

        $game->setTriesLeft();
        $game->setDottedWord();
        $game->setStatus();

        $this->assertSame($game->getStatus(), 'fail');
    }


    public function testSetStatusSucces()
    {
        $game = new Game();
        $game->setWord('aardvarks');
        $game->addGuessedLetters('a');
        $game->addGuessedLetters('r');
        $game->addGuessedLetters('d');
        $game->addGuessedLetters('v');
        $game->addGuessedLetters('r');
        $game->addGuessedLetters('k');
        $game->addGuessedLetters('s');
        $game->setTriesLeft();
        $game->setDottedWord();
        $game->setStatus();

        $this->assertSame($game->getStatus(), 'succes');
    }

    public function testToArray()
    {

        $gameArray = [];
        $gameArray['id'] = null;
        $gameArray['word'] = 'aa...a...';
        $gameArray['status'] = 'busy';
        $gameArray['tries_left'] = 7;

        $game = new Game();
        $game->setWord('aardvarks');
        $game->addGuessedLetters('a');
        $game->setTriesLeft();
        $game->setDottedWord();
        $game->setStatus();

        $this->assertSame($game->toArray(), $gameArray);

    }
}
