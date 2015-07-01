<?php

namespace Houdtbaar\HangmanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="Houdtbaar\HangmanBundle\Repository\GameRepository")
 */
class Game
{

    const LETTER_REPLACEMENT = '.';
    const TOTAL_NUM_OF_TRIES = 7;

    const STATUS_BUSY = 'busy';
    const STATUS_SUCCES = 'succes';
    const STATUS_FAIL = 'fail';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $word;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $dottedWord;

    /**
     * @ORM\Column(type="integer")
     */
    protected $triesLeft;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $status;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    protected $guessedLetters;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    protected $triedLetters;


    /**
     * Sets default values for properties on initiation
     */
    public function __construct()
    {
        $this->guessedLetters = [];
        $this->triedLetters = [];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * @param string $word
     */
    public function setWord($word)
    {
        $this->word = $word;
    }

    /**
     * @return mixed
     */
    public function getDottedWord()
    {
        return $this->dottedWord;
    }

    /**
     *  Generated a version of the word with the guessed letters showing and the other replaces by dots
     */
    public function setDottedWord()
    {

        $dottedWord = [];
        $wordParts = str_split($this->word);

        $guessedLetters = $this->getGuessedLetters();

        foreach ($wordParts as $part) {
            if (in_array($part, $guessedLetters) === true) {
                $dottedWord[] = $part;
            } else {
                $dottedWord[] = static::LETTER_REPLACEMENT;
            }
        }
        //print_r($dottedWord); exit();
        $this->dottedWord = implode('', $dottedWord);
    }

    /**
     * @return mixed
     */
    public function getTriesLeft()
    {
        return $this->triesLeft;
    }

    /**
     * @param mixed $triesLeft
     */
    public function setTriesLeft()
    {
        $wrongLetters = array_diff_assoc(
            $this->getTriedLetters(),
            $this->getGuessedLetters()
        );

        $triesLeft = static::TOTAL_NUM_OF_TRIES - count($wrongLetters);

        $this->triesLeft = $triesLeft;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the status according to busy, succes or fail
     */
    public function setStatus()
    {

        // set the masked word, just to be sure
        $this->setDottedWord();

        if (strpos(
                $this->getDottedWord(),
                static::LETTER_REPLACEMENT
            ) === false
        ) {

            $this->status = static::STATUS_SUCCES;

        } else {
            if ($this->getTriesLeft() == 0) {

                $this->status = static::STATUS_FAIL;

            } else {

                $this->status = static::STATUS_BUSY;

            }
        }
    }

    /**
     * @return array
     */
    public function getGuessedLetters()
    {
        return $this->guessedLetters;
    }

    /**
     * @param mixed $guessedLetters
     */
    public function setGuessedLetters($guessedLetters)
    {
        $this->guessedLetters = $guessedLetters;
    }

    /**
     * @param mixed $guessedLetters
     */
    public function addGuessedLetters($guessedLetters)
    {
        $this->guessedLetters[] = $guessedLetters;
    }

    /**
     * @return array
     */
    public function getTriedLetters()
    {
        return $this->triedLetters;
    }

    /**
     * @param mixed $triedLetters
     */
    public function setTriedLetters($triedLetters)
    {
        $this->triedLetters = $triedLetters;
    }

    /**
     * @param mixed $triedLetters
     */
    public function addTriedLetters($triedLetters)
    {
        $this->triedLetters[] = $triedLetters;
    }

    public function getWordUniqueLetters()
    {
        return array_unique(str_split($this->word));
    }

    public function getWordInLetters()
    {
        return array_unique(str_split($this->word));
    }

    /**
     * returns this object as an array
     * @return array
     */
    public function toArray()
    {

        $response = [];

        $response['id'] = $this->id;
        $response['word'] = $this->dottedWord;
        $response['status'] = $this->status;
        $response['tries_left'] = $this->triesLeft;

        return $response;
    }
}
