<?php

declare(strict_types=1);

namespace App\Services;

final class HomeworkAiService
{
    public function available(): bool
    {
        return false;
    }

    public function placeholderFeatures(): array
    {
        return [
            'Generate homework questions',
            'Generate marking rubric',
            'Explain homework instructions',
            'Summarize student performance',
            'Suggest improvement areas',
        ];
    }
}
