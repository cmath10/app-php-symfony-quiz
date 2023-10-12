<?php

namespace App\Persistence\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 't_answers')]
#[ORM\Entity]
class Answer
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Variant::class)]
    private Variant $variant;

    #[ORM\Id]
    #[ORM\ManyToOne(
        targetEntity: Attempt::class,
        inversedBy: 'answers'
    )]
    private Attempt $attempt;

    public function __construct(Variant $variant, Attempt $attempt)
    {
        $this->variant = $variant;
        $this->attempt = $attempt;
    }

    public static function byQuestion(Question $question): \Closure
    {
        return fn (Answer $a) => $a->belongsTo($question);
    }

    public static function byVariant(Variant $variant): \Closure
    {
        return fn (Answer $a) => $a->belongsTo($variant);
    }

    public function getVariant(): Variant
    {
        return $this->variant;
    }

    public function getAttempt(): Attempt
    {
        return $this->attempt;
    }

    public function identicalTo(Answer $answer): bool
    {
        return $this->belongsTo($answer->getVariant())
            && $this->belongsTo($answer->getAttempt());
    }

    public function belongsTo(Question|Variant|Attempt $entity): bool
    {
        if ($entity instanceof Question) {
            return $this->variant->belongsTo($entity);
        }

        if ($entity instanceof Variant) {
            return $this->variant->identicalTo($entity);
        }

        return $this->attempt->getId() === $entity->getId();
    }
}