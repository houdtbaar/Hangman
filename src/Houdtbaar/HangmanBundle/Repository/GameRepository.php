<?php

namespace Houdtbaar\HangmanBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Houdtbaar\HangmanBundle\Entity\Game;
use Doctrine\ORM\Query;

class GameRepository extends EntityRepository
{

    /**
     * Checks if a letter exists in a word and handles the updating of the Game entity
     * @param $char
     * @param \Houdtbaar\HangmanBundle\Entity\Game $game
     * @return $char
     */
    public function checkLetter($char, Game $game)
    {

        $wordInLetters = $game->getWordInLetters();

        // check if input is a string
        if (preg_match('/^[a-z]$/', $char) === 0) {
            return null;
        }

        if (in_array(strtolower($char), $wordInLetters) === true) {

            $game->addGuessedLetters($char);
            $game->addTriedLetters($char);

        } else {

            $game->addTriedLetters($char);

        }
        $game->setDottedWord();
        $game->setTriesLeft();
        $game->setStatus();

        $this->saveGame($game);

        return $char;
    }

    /**
     * Persist game to datasource
     * @param \Houdtbaar\HangmanBundle\Entity\Game $game
     */
    public function saveGame(Game $game)
    {

        $this->getEntityManager()->persist($game);
        $this->getEntityManager()->flush();
    }

    /**
     * Adds a new Game object with basic properties to the database
     * @param $word
     * @return mixed
     */
    public function addGame($word)
    {

        $game = new Game();
        $game->setWord($word);
        $game->setDottedWord();
        $game->setTriesLeft();
        $game->setStatus();

        $this->getEntityManager()->persist($game);
        $this->getEntityManager()->flush();

        return $game->getId();
    }

    /**
     * Returns an array of Games from the database limit by the $limit parameter
     * @param $limit
     * @return array
     */
    public function getGames($limit = 10)
    {

        $query = $this->getEntityManager()->createQuery(
            'SELECT g FROM HoudtbaarHangmanBundle:Game g'
        );

        return $query->setMaxResults($limit)->getResult();
    }

    /**
     * Converts a array of Game objects to an array of arrays with Game properties
     * @param array of games
     * @return array of game data
     */
    public function getGamesOverview($games)
    {

        $response = [];

        if ($games !== null) {

            foreach ($games as $game) {

                $response[] = $game->toArray();

            }
        }

        return $response;
    }

    /**
     * Returns a Game object as an array, for the given $id
     * @param $id
     * @return array
     */
    public function findGameById($id)
    {

        $game = $this->findOneBy(array('id' => $id));

        $respone = [];

        if ($game !== null) {
            return $game->toArray();
        }

        return $respone;
    }

}
