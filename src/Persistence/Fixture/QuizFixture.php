<?php

namespace App\Persistence\Fixture;

use App\Persistence\Entity\Variant;
use App\Persistence\Entity\Question;
use App\Persistence\Entity\Quiz;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class QuizFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $quiz = new Quiz('Arithmetics');
        $quiz
            ->addQuestion(
                Question::create('1 + 1 = ')
                    ->addVariant(new Variant('3'))
                    ->addVariant(new Variant('2', isCorrect: true))
                    ->addVariant(new Variant('0'))
            )
            ->addQuestion(
                Question::create('2 + 2 = ')
                    ->addVariant(new Variant('4', isCorrect: true))
                    ->addVariant(new Variant('3 + 1', isCorrect: true))
                    ->addVariant(new Variant('10'))
            )
            ->addQuestion(
                Question::create('3 + 3 = ')
                    ->addVariant(new Variant('1 + 5', isCorrect: true))
                    ->addVariant(new Variant('1'))
                    ->addVariant(new Variant('6', isCorrect: true))
                    ->addVariant(new Variant('2 + 4', isCorrect: true))
            )
        ;

        $manager->persist($quiz);

        $manager->flush();
    }
}
