<?php

declare(strict_types=1);

namespace App;

final class View
{
    public function __construct(private string $viewPath)
    {
    }

    public function render(string $template, array $data = []): string
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require $this->viewPath . '/' . $template . '.php';
        $content = ob_get_clean();

        ob_start();
        require $this->viewPath . '/layout.php';

        return ob_get_clean();
    }
}
