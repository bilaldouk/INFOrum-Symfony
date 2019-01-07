<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Question;
use App\Entity\Answer;
use App\Repository\QuestionRepository;
use App\Form\QuestionType;


/**
 * @method api
 * @method createQuestion
 * @method editQuestion
 * @method deleteQuestion
 * @method showQuestion
 * @method createAnswer
 * @method editAnswer
 * @method deleteAnswer
 * @method showAnswer
 */
class QuestionAPIController extends AbstractController
{
    /**
     * @Route("/api", name="api_questions", methods ={"GET"})
     */
    public function api(QuestionRepository $repo)
    {
        $response = new Response();
        $encoders = array(new JsonEncoder());

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);

        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $question = $repo->findAll();

        $json = $serializer->serialize($question, 'json');

        $response->setContent($json);
        $response->headers->set('Content-Type', 'application/json');

        $response->setStatusCode('200');

        return $response;
    }

    /**
     * @Route("/api/new", name="api_question_create", methods ={"POST", "OPTIONS"})
     */
    public function createQuestion(Question $question = null, Request $request, ObjectManager $manager)
    {
        $response = new Response();
        $encoders = array(new JsonEncoder());

        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            $response = new Response();
            $response->headers->set('Content-Type', 'application/text');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type', true);

            return $response;
        }

        $json = $request->getContent();
        $content = json_decode($json, true);

        $query = array();

        
        $normalizer->setCircularReferenceLimit(2);

        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);

        $question = $repo->findAll();

        $json = $serializer->serialize($question, 'json');

        $response->setContent($json);
        $response->headers->set('Content-Type', 'application/json');

        $response->setStatusCode('200');

        return $response;
    }
}