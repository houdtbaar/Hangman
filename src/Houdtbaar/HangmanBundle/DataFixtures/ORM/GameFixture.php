<?php

namespace Houdtbaar\HangmanBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Houdtbaar\HangmanBundle\Entity\Game;

/**
 * Generated by Webonaute\DoctrineFixtureGenerator.
 *
 */
class GameFixture extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Set loading order.
     *
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }


    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->getClassMetaData(get_class(new Game()))->setIdGeneratorType(
            ClassMetadata::GENERATOR_TYPE_NONE
        );

        $item1 = new Game();
        $item1->setId(1);
        $item1->setWord("advantageous");
        $item1->setGuessedLetters(unserialize('a:1:{i:0;s:1:"a";}'));
        $item1->setTriedLetters(unserialize('a:1:{i:0;s:1:"a";}'));
        $item1->setTriesLeft();
        $item1->setDottedWord();
        $item1->setStatus();
        $manager->persist($item1);

        $item2 = new Game();
        $item2->setId(2);
        $item2->setWord("ab");
        $item2->setGuessedLetters(
            unserialize('a:2:{i:0;s:1:"a";i:1;s:1:"b";}')
        );
        $item2->setTriedLetters(unserialize('a:2:{i:0;s:1:"a";i:1;s:1:"b";}'));
        $item2->setTriesLeft();
        $item2->setDottedWord();
        $item2->setStatus();
        $manager->persist($item2);
        $manager->persist($item2);

        $item3 = new Game();
        $item3->setId(3);
        $item3->setWord("aardvarks");
        $item3->setGuessedLetters(unserialize('a:0:{}'));
        $item3->setTriedLetters(unserialize('a:0:{}'));
        $item3->setTriesLeft();
        $item3->setDottedWord();
        $item3->setStatus();
        $manager->persist($item3);

        $manager->flush();
    }

}
