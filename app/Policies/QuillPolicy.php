<?php
namespace App\Policies;

use Spatie\Csp\Policies\Basic;

class QuillPolicy extends Basic
{
    public function configure()
    {
        parent::configure();

        $this->addDirective('script-src', [
            'self',
            'https://cdn.jsdelivr.net',
            "'unsafe-eval'", // Для Quill
        ])->addDirective('style-src', [
            'self',
            'https://cdn.jsdelivr.net',
            "'unsafe-inline'", // Для стилей Quill
        ]);
    }
}
