<?php

namespace App\Persistence\Entity;

use App\Persistence\Exception\NoVariantException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Table(name: 't_questions')]
#[ORM\Entity]
class Question
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $text;

    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    private Quiz|null $quiz = null;

    #[ORM\OneToMany(
        mappedBy: 'question',
        targetEntity: Variant::class,
        cascade: ['persist', 'remove']
    )]
    private Collection $variants;

    public function __construct(string $text)
    {
        $this->text = $text;
        $this->variants = new ArrayCollection();
    }

    public static function create(string $text): Question
    {
        return new Question($text);
    }

    /** @noinspection PhpUnused */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata
            ->addPropertyConstraints('text', [
                new Assert\NotBlank(message: 'Question text should not be empty'),
            ])
            ->addPropertyConstraints('quiz', [
                new Assert\NotNull(message: 'Quiz entity should be specified'),
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

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(Quiz $quiz): Question
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function getVariants(): Collection
    {
        return $this->variants;
    }

    public function findVariant(\Closure $p): ?Variant
    {
        $variant = $this->variants->filter($p)->first();
        if (false === $variant) {
            return null;
        }

        return $variant;
    }

    public function getVariant(int $id): Variant
    {
        $variant = $this->findVariant(fn (Variant $v) => $v->getId() === $id);

        if (null === $variant) {
            throw new NoVariantException($id);
        }

        return $variant;
    }

    public function addVariant(Variant $variant): Question
    {
        $existing = $this->variants->filter(fn (Variant $v) => $v->sameAs($variant))->first();

        if (false === $existing) {
            $this->variants->add($variant->setQuestion($this));
        }

        return $this;
    }

    public function everyVariant(\Closure $p): bool
    {
        return $this->variants->reduce(fn ($every, Variant $v) => $every && $p($v), true);
    }

    public function identicalTo(Question $question): bool
    {
        return $this->id && $this->id === $question->id;
    }

    public function sameAs(Question $question): bool
    {
        return $this->identicalTo($question) || $this->text === $question->text;
    }
}