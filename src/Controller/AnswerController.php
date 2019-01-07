<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Question;
use App\Entity\Answer;
use App\Repository\QuestionRepository;
use App\Repository\AnswerRepository;

class AnswerController extends AbstractController
{    
    /**
     * @Route("/question/{id}/answer/{a_id}/show", name="show_answer")
     */
    public function showAnswer(Request $request, $a_id)
    {
        if ($a_id) {
            $user = $this->getUser();
            $rep = $this->getDoctrine()->getRepository(Answer::class);
            $answer = $rep->find($a_id);
            return $this->render('inforum/show_answer.html.twig', [
                'answer' => $answer
            ]);
        }
        else {
            return $this->render('inforum/show_answer.html.twig');
        }

        
    }

    /**
     * @Route("/question/{id}/answer/new", name="create_answer")
     * @Route("/answer/{a_id}/edit", name="edit_answer")
     */
    public function create_answer(Request $request, $a_id)
    {
        if(!$a_id){
            $answer = new Answer();
        }
        
        $user = $this->getUser();
        $rep = $this->getDoctrine()->getRepository(Answer::class);
        $answer = $rep->find($a_id);
        
        $form = $this->createFormBuilder($answer)
                     ->add('answer')
                     ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            if(!$answer->getId()){
                $answer->setUser($user);
                $answer->setQuestion($question);
                $answer->setCreatedAt(new \DateTime());
            }
            $manager->persist($answer);
            $manager->flush();

            return $this->render('inforum/show_answer.html.twig', [
                'answer' => $answer,
                'form' => $form->createView()
            ]);
        }

        return $this->render('inforum/edit_answer.html.twig', [
            'answer' => $answer
        ]);
    }

    /**
     * @Route("/answer/{a_id}/delete", name="delete_answer")
     */
    public function deleteAnswer(Request $request, $a_id)
    {
        try{
            $user = $this->getUser();
            $rep = $this->getDoctrine()->getRepository(Answer::class);
            $repo = $this->getDoctrine()->getRepository(Question::class);
        
            if ($a_id != null) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $answer = $em->getRepository(Answer::class)->find($a_id);
                    

                    $em->remove($answer);
                    $em->flush();
                    $this->addFlash('notice', "Delete with success");
                    return $this->redirectToRoute('mypage_answers', ['id' => $user->getId()]);
                }
                catch (Exception $e) {
                    $this->addFlash('notice', "Doesn't delete with success");
                }
            }
        }
        catch (Exception $e) {
            $this->addFlash('notice', "Error");
        }
    }
}
