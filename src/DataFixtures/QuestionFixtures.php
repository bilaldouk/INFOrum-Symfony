<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Question;
use App\Entity\Answer;
use App\Entity\User;

class QuestionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        for($o = 1; $o <= 10; $o++){
            $user = new User();

            $user->setAlias($faker->name)
                 ->setEmail($faker->email)
                 ->setPassword($faker->word);
            
            
            $manager->persist($user);

            for($i = 1; $i <= 8; $i++) {
                $question = new Question();
                $question->setTitle($faker->sentence())
                         ->setContent($faker->paragraph())
                         ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                         ->setCategory($faker->sentence())
                         ->setUser($user);

                $manager->persist($question);

                for($j = 1; $j <= mt_rand(3, 4); $j++) {
                    $answer = new Answer();
                    $now = new \DateTime();
                    $interval = $now->diff($question->getCreatedAt());
                    $ivl = $interval->days;
                    $minimum = '-' . $ivl . 'hours';

                    $answer->setAnswer($faker->paragraph())
                           ->setCreatedAt($faker->dateTimeBetween($minimum))
                           ->setQuestion($question)
                           ->setUser($user);
                    
                    $manager->persist($answer);
                }
            }
        }

        $manager->flush();
    }
}
