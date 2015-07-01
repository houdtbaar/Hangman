<?php

namespace Houdtbaar\HangmanBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class WordRepository extends EntityRepository
{
    /**
     * Returns a Word object from the datasource
     * @param $word
     * @return array
     */
    public function findOneByWord($word)
    {

        $query = $this->getEntityManager()->createQuery(
            'SELECT w FROM HoudtbaarHangmanBundle:Word w WHERE word=:word'
        );
        $query->setParameter(':word', $word);

        return $query->setMaxResults(1)->getResult();
    }

    /**
     * Returns a random Word from the datasource
     * @return array
     */
    public function findRandom()
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT w FROM HoudtbaarHangmanBundle:Word w'
        );
        $allWords = $query->getResult();

        return $allWords[rand(0, count($allWords) - 1)];
    }


    /**
     * Gets a list of random words, the number of words is limited by the parameter $limit
     * @param int $limit
     * @return array
     */
    public function getRandomList($limit = 10)
    {

        $ramdomList = [];
        $word = null;

        for ($x = 0; $x < $limit; $x++) {

            $word = $this->findRandom();

            if (in_array($word->getWord(), $ramdomList)) {
                $word = $this->findRandom();
            }

            $ramdomList[$word->getWord()] = $word->getWord();
        }

        return $ramdomList;
    }
}