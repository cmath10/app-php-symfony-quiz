<?php

namespace App\Persistence\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Table(name: 't_variants')]
#[ORM\Entity]
class Variant
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $text;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isCorrect;

    #[ORM\ManyToOne(targetEntity: Question::class)]
    private Question|null $question;

    public function __construct(string $text, bool $isCorrect = false)
    {
        $this->text = $text;
        $this->isCorrect = $isCorrect;
    }

    /** @noinspection PhpUnused */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata
            ->addPropertyConstraints('text', [
                new Assert\NotBlank(message: 'Answer variant text should not be empty'),
            ])
            ->addPropertyConstraints('question', [
                new Assert\NotNull(message: 'Question entity should be specified'),
            ])
        ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): Variant
    {
        $this->question = $question;
        return $this;
    }

    public function isCorrect(): bool
    {
        return $this->isCorrect;
    }

    public function identicalTo(Variant $variant): bool
    {
        return $this->id && $this->id === $variant->id;
    }

    public function sameAs(Variant $variant): bool
    {
        return $this->identicalTo($variant) || $this->text === $variant->text;
    }

    public function belongsTo(Question $question): bool
    {
        return $this->question?->identicalTo($question) ?? false;
    }
}