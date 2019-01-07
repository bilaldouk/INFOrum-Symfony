<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\User;
use App\Entity\Question;
use App\Entity\Answer;
use App\Repository\QuestionRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class InforumController extends AbstractController
{
    /**
     * @Route("/", name = "home_guest")
     */
    public function home_guest() {
        $repo = $this->getDoctrine()->getRepository(Question::class);

        $questions = $repo->findAll();
        
        return $this->render('inforum/home_guest.html.twig', [
            'questions' => $questions
        ]);
    }

    /**
     * @Route("/question/{id}/show"), name="show_question")
     */
    public function show_question(Answer $answer = null, Request $request, QuestionRepository $repo, ObjectManager $manager, $id) {

        $question = $repo->find($id);
        $user = $this->getUser();

        $rep = $this->getDoctrine()->getRepository(Answer::class);
        $answers = $rep->findBy(array('question' => $question));
        $questions = $repo->findAll();
        
        $form = $this->createFormBuilder($answer)
                    ->add('answer')
                    ->getForm();

        if(!$answer){
            $answer = new Answer();
        
        }
        
        

        $form->handleRequest($request);
        

        if($form->isSubmitted() && $form->isValid()) {

            if(!$answer->getId()){
                $answer->setUser($user);
                $answer->setQuestion($question);
                $answer->setCreatedAt(new \DateTime());
            }
            $manager->persist($answer);
            $manager->flush();

            return $this->render('inforum/home_user.html.twig', [
                'questions' => $questions
            ]);
        }

        return $this->render('inforum/show_question.html.twig', [
            'question' => $question,
            'answers' => $answers,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/home", name="home_user")
     */
    public function home_user() 
    {
        $repo = $this->getDoctrine()->getRepository(Question::class);

        $questions = $repo->findAll();

        return $this->render('inforum/home_user.html.twig', [
            'questions' => $questions
        ]);
    }

    /**
     * @Route("/user/{id}", name="mypage_questions")
     */
    public function mypage_questions() 
    {
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository(Question::class);
        $questions = $repo->findBy(array('user' => $user));

        return $this->render('inforum/mypage_questions.html.twig', [
            'questions' => $questions
        ]);
    }

    
    /**
     * @Route("/user/{id}/answers", name="mypage_answers")
     */
    public function mypage_answers()
    {
        
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository(Answer::class);
        $answers = $repo->findBy(array('user' => $user));

        return $this->render('inforum/mypage_answers.html.twig', [
            'answers' => $answers
        ]);
    }

    /**
     * @Route("/new", name="ask_question")
     * @Route("/question/{id}/edit", name="edit_question")
     */
    public function ask_question(Question $question = null, Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();

        if(!$user){
            return $this->redirectToRoute('home');
        }

        if(!$question){
            $question = new Question();
        }

        $form = $this->createFormBuilder($question)
                     ->add('title')
                     ->add('category')
                     ->add('content')
                     ->getForm();
        
        $form->handleRequest($request);

        $rep = $this->getDoctrine()->getRepository(Answer::class);
        $answers = $rep->findBy(array('question' => $question));

        if($form->isSubmitted() && $form->isValid()) {

            if(!$question->getId()){
                $question->setUser($user);
                $question->setCreatedAt(new \DateTime());
            }
            $manager->persist($question);
            $manager->flush();

            return $this->render('inforum/show_question.html.twig', [
                'question' => $question,
                'answers' => $answers
            ]);
        }
        
        return $this->render('inforum/ask_question.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/question/{id}/delete", name="delete_question")
     */
    public function delete_question(Request $request, $id)
    {
        try{
            $user = $this->getUser();
            $rep = $this->getDoctrine()->getRepository(Answer::class);
            $repo = $this->getDoctrine()->getRepository(Question::class);
            
        
            if ($id != null) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $question = $em->getRepository(Question::class)->find($id);

                    $em->remove($question);
                    $em->flush();

                    $this->addFlash('message', "You deleted the question with success");
                    return $this->redirectToRoute('home_user', ['id' => $user->getId()]);
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
