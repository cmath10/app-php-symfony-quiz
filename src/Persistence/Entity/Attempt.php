<?php

namespace App\Persistence\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 't_quiz_attempts')]
#[ORM\Entity]
class Attempt
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    private Quiz $quiz;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\OneToMany(
        mappedBy: 'attempt',
        targetEntity: Answer::class,
        cascade: ['persist', 'remove']
    )]
    private Collection $answers;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $startedAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $finishedAt = null;

    public function __construct(Quiz $quiz, User $user)
    {
        $this->quiz = $quiz;
        $this->user = $user;
        $this->answers = new ArrayCollection();
        $this->startedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function findAnswer(\Closure $p): ?Answer
    {
        $answer = $this->answers->filter($p)->first();

        return false === $answer ? null : $answer;
    }

    public function addAnswer(Answer $answer): Answer
    {
        $existing = $this->findAnswer(fn (Answer $a) => $a->identicalTo($answer));

        if (null === $existing) {
            $this->answers->add($answer);
            return $answer;
        }

        return $existing;
    }

    public function hasVariant(Variant $variant): bool
    {
        return null !== $this->findAnswer(Answer::byVariant($variant));
    }

    public function addVariant(Variant $variant): Answer
    {
        return $this->addAnswer(new Answer($variant, $this));
    }

    public function getStartedAt(): \DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeImmutable $finishedAt): Attempt
    {
        $this->finishedAt = $finishedAt;
        return $this;
    }

    public function getDeadline(): \DateTimeImmutable
    {
        return $this->startedAt->add(new \DateInterval('PT15M'));
    }

    public function isOutdated(): bool
    {
        return $this->getDeadline()->getTimestamp() < \time();
    }

    public function isAnswered(Question $question): bool
    {
        return null !== $this->findAnswer(Answer::byQuestion($question));
    }

    public function isAnsweredCorrectly(Question $question): bool
    {
        return $question->everyVariant(function (Variant $v) {
            return $v->isCorrect() && $this->hasVariant($v) || !$v->isCorrect() && !$this->hasVariant($v);
        });
    }

    public function isFinished(): bool
    {
        return null !== $this->finishedAt || $this->quiz->getQuestions()->reduce(function (bool $all, Question $question): bool {
            return $all && $this->isAnswered($question);
        }, true);
    }
}