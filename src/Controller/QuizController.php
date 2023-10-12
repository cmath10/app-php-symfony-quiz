<?php

namespace App\Controller;

use App\Persistence\Entity\Answer;
use App\Persistence\Entity\Question;
use App\Persistence\Entity\Quiz;
use App\Persistence\Entity\Attempt;
use App\Persistence\Entity\User;
use App\Persistence\Entity\Variant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

final class QuizController extends AbstractController
{
    public const QUIZ_LIST_ROUTE = 'app_quiz_list';
    public const QUIZ_ATTEMPT_ROUTE = 'app_quiz_attempt';

    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    #[Route('/', name: self::QUIZ_LIST_ROUTE)]
    public function list(): Response
    {
        $list = $this->em->getRepository(Quiz::class)->findBy([]);
        $user = $this->em->getRepository(User::class)->findOneBy([
            'nickname' => $this->getUser()?->getUserIdentifier(),
        ]);

        return $this->render('quiz/list.html.twig', [
            'list' => $list,
            'user' => $user,
        ]);
    }

    #[Route('/quiz/{id}/attempt', name: self::QUIZ_ATTEMPT_ROUTE)]
    public function attempt(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY);

        $quiz = $this->em->getRepository(Quiz::class)->findOneBy(['id' => $id]);
        $user = $this->em->getRepository(User::class)->findOneBy([
            'nickname' => $this->getUser()?->getUserIdentifier(),
        ]);

        $lastAttempt = $this->em->getRepository(Attempt::class)->findOneBy([
            'quiz' => $quiz,
            'user' => $user,
        ], ['id' => 'DESC']);

        if (!$request->request->has('questions') && (null === $lastAttempt || $lastAttempt->isOutdated())) {
            $attempt = new Attempt($quiz, $user);

            $this->em->persist($attempt);
            $this->em->flush();
        } else {
            $attempt = $lastAttempt;
        }

        if ($request->request->has('questions')) {
            $data = $request->request->all()['questions'] ?? [];

            if (!$attempt->isFinished() && !$attempt->isOutdated()) {
                if ($this->updateAttempt($attempt, $data)) {
                    if ($attempt->isFinished()) {
                        $attempt->setFinishedAt(new \DateTimeImmutable());
                    }
                    $this->em->flush();
                }
            }
        }

        return $this->render('quiz/attempt.html.twig', [
            'quiz' => $quiz,
            'lastAttempt' => $lastAttempt,
            'attempt' => $attempt,
        ]);
    }

    private function updateAttempt(Attempt $attempt, array $data): bool
    {
        $quiz = $attempt->getQuiz();

        return $quiz->getQuestions()->reduce(function (bool $hasChanges, Question $q) use ($attempt, $data): bool {
            $chosen = $data[$q->getId()] ?? [];

            return (!empty($chosen)
                    ? $this->chooseVariants($attempt, $q, $chosen)
                    : $this->removeAnswersByQuestion($attempt, $q))
                || $hasChanges;
        }, false);
    }

    private function removeAnswersByQuestion(Attempt $attempt, Question $question): bool
    {
        $answers = $attempt->getAnswers();
        $hasChanges = false;

        foreach ($answers->filter(Answer::byQuestion($question)) as $answer) {
            $answers->removeElement($answer);
            $this->em->remove($answer);
            $hasChanges = true;
        }

        return $hasChanges;
    }

    private function chooseVariants(Attempt $attempt, Question $question, array $chosen): bool
    {
        return $question->getVariants()->reduce(fn(bool $hasChanges, Variant $variant): bool => (
            \in_array($variant->getId(), $chosen)
                ? $this->addVariant($attempt, $variant)
                : $this->removeVariant($attempt, $variant)
            ) || $hasChanges, false);
    }

    private function addVariant(Attempt $attempt, Variant $variant): bool
    {
        if (!$attempt->hasVariant($variant)) {
            $this->em->persist($attempt->addVariant($variant));
            return true;
        }

        return false;
    }

    private function removeVariant(Attempt $attempt, Variant $variant): bool
    {
        $answer = $attempt->findAnswer(Answer::byVariant($variant));
        if (null !== $answer) {
            $attempt->getAnswers()->removeElement($answer);
            $this->em->remove($answer);
            return true;
        }

        return false;
    }
}