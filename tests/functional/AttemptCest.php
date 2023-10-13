<?php

namespace App\Tests\Functional;

use App\Persistence\Entity\Question;
use App\Persistence\Entity\Quiz;
use App\Persistence\Entity\User;
use App\Persistence\Entity\Variant;
use App\Tests\Data\Fixture\CallableFixture;
use App\Tests\Infrastructure\FunctionalTester;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AttemptCest
{
    public function testAllQuestionsPresent(FunctionalTester $I): void
    {
        $I->loadFixtures([$this->getFixture($I)], append: false);

        $I->amOnPage('/sign-in');

        $I->submitForm('form', ['nickname' => 'tester', 'password' => 'tester']);

        $I->dontSee('Invalid credentials');
        $I->see('Welcome, tester!');

        $quiz = $I->grabEntityFromRepository(Quiz::class, ['name' => 'Arithmetics']);

        $I->amOnPage(\sprintf('/quiz/%s/attempt', $quiz->getId()));
        $I->see('Arithmetics');

        $I->see('1 + 1 = ', 'h5');

        $I->see('3', 'label');
        $I->see('2', 'label');
        $I->see('0', 'label');

        $I->see('2 + 2 = ', 'h5');
        $I->see('4', 'label');
        $I->see('3 + 1', 'label');
        $I->see('10', 'label');
    }

    private function getFixture(FunctionalTester $I): CallableFixture
    {
        return new CallableFixture(static function (ObjectManager $em) use ($I) {
            /** @var UserPasswordHasherInterface $hasher */
            $hasher = $I->grabService('security.password_hasher');

            $tester = new User();
            $tester
                ->setNickname('tester')
                ->setPassword($hasher->hashPassword($tester, 'tester'))
            ;

            $em->persist($tester);

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
            ;

            $em->persist($quiz);

            $em->flush();
        });
    }
}