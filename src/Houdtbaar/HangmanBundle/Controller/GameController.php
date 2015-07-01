<?php

namespace Houdtbaar\HangmanBundle\Controller;

use Doctrine\ORM\EntityManager;
use Houdtbaar\HangmanBundle\Repository\GameRepository;
use Houdtbaar\HangmanBundle\Repository\WordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;


class GameController extends Controller
{

    private $wordRepository;
    private $gameRepository;
    private $requestStack;
    private $entityManager;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(
        EntityManager $entityManager,
        RequestStack $requestStack,
        GameRepository $gameRepository,
        WordRepository $wordRepository
    ) {
        $this->entityManager = $entityManager;
        $this->wordRepository = $wordRepository;
        $this->gameRepository = $gameRepository;

        $this->requestStack = $requestStack;
    }


    /**
     * Formats the $data to a JSON response with a status
     * @param $data
     * @param $status
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createJsonResponse($data, $status)
    {

        $response = array('status' => $status, 'data' => $data);

        return new JsonResponse($response, $status);
    }


    /**
     * Generates an overview of games played
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gameOverviewAction()
    {

        $games = $this->gameRepository->getGames(10);
        $response = $this->gameRepository->getGamesOverview($games);

        return $this->createJsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Starts a new game
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gameNewAction()
    {

        $word = $this->wordRepository->findRandom();
        $id = $this->gameRepository->addGame($word->getWord());

        return $this->createJsonResponse($id, Response::HTTP_OK);
    }

    /**
     * Creates a response for the current game
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function gameResponseAction($id)
    {

        $response = $this->gameRepository->findGameById($id);

        return $this->createJsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Action that responds to guessing a letter
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function gameGuessAction($id)
    {

        $data = json_decode(
            $this->requestStack->getCurrentRequest()->getContent()
        );
        $char = trim(strtolower($data->char));


        if (strlen($char) > 1) {
            return $this->createJsonResponse(
                array(
                    'response' => 'Dit zijn meerdere letters, meerdere letters tegelijk proberen is niet toegestaan.',
                    'char' => $data->char
                ),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if (preg_match('/^[a-z]$/', $char) === 0) {
            return $this->createJsonResponse(
                array(
                    'response' => 'Dit is geen letter.',
                    'char' => $data->char
                ),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $game = $this->gameRepository->findOneBy(array('id' => $id));
        $this->gameRepository->checkLetter($char, $game);

        $response = $this->gameRepository->findGameById($id);

        return $this->createJsonResponse(
            $response,
            Response::HTTP_OK
        );
    }
}
