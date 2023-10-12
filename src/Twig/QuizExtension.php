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
            new TwigFilter('time_left', [$this, 'timeLeft']),
        ];
    }

    public function timeLeft(Attempt $attempt): string
    {
        $minutes = 0;
        $seconds = 0;

        $total = $attempt->getDeadline()->getTimestamp() - \time();

        if ($total >= 0) {
            $minutes = (int) \floor($total / 60);
            $seconds = (int) ($total - $minutes * 60);
        }

        return \sprintf('%s:%s', $this->pad($minutes), $this->pad($seconds));
    }

    private function pad(int $value): string
    {
        return \str_pad("$value", 2, '0', STR_PAD_LEFT);
    }
}