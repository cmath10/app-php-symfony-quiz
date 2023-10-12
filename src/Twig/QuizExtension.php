<?php

namespace App\Twig;

use App\Persistence\Entity\Attempt;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class QuizExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_elapsed', [$this, 'timeElapsed']),
            new TwigFilter('time_left', [$this, 'timeLeft']),
        ];
    }

    public function timeElapsed(Attempt $attempt): string
    {
        $startedAt = $attempt->getStartedAt();
        $finishedAt = $attempt->isOutdated()
            ? $attempt->getFinishedAt() ?? new \DateTimeImmutable()
            : $attempt->getDeadline()
        ;

        $elapsed = $finishedAt->getTimestamp() - $startedAt->getTimestamp();

        return $this->format($elapsed);
    }

    public function timeLeft(Attempt $attempt): string
    {
        $left = $attempt->getDeadline()->getTimestamp() - \time();

        return $this->format($left);
    }

    private function format(int $totalSeconds): string
    {
        $minutes = 0;
        $seconds = 0;

        if ($totalSeconds >= 0) {
            $minutes = (int) \floor($totalSeconds / 60);
            $seconds = (int) ($totalSeconds - $minutes * 60);
        }

        return \sprintf('%s:%s', $this->pad($minutes), $this->pad($seconds));
    }

    private function pad(int $value): string
    {
        return \str_pad("$value", 2, '0', STR_PAD_LEFT);
    }
}