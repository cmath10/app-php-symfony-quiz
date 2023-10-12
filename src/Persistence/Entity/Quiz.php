<?php

namespace App\Persistence\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Table(name: 't_quiz')]
#[ORM\Entity]
class Quiz
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    #[ORM\OneToMany(
        mappedBy: 'quiz',
        targetEntity: Question::class,
        cascade: ['persist', 'remove']
    )]
    private Collection $questions;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->questions = new ArrayCollection();
    }

    /** @noinspection PhpUnused */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata
            ->addPropertyConstraints('name', [
                new Assert\NotBlank(message: 'Quiz name should not be empty'),
            ])
        ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): Quiz
    {
        $existing = $this->questions->filter(fn (Question $q) => $q->sameAs($question))->first();

        if (false === $existing) {
            $this->questions->add($question->setQuiz($this));
        }

        return $this;
    }
}