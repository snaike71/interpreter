<?php

namespace Snaik\Interpreter\Services;

use Symfony\Component\Console\Style\SymfonyStyle;

class InputOutput extends SymfonyStyle
{
   
    public function right(string $message): void
    {
        $this->block(sprintf(' %s', $message), null, 'fg=white;bg=green', ' ', true);
    }

    public function wrong(string $message): void
    {
        $this->block(sprintf(' 😮  %s', $message), null, 'fg=white;bg=red', ' ', true);
    }
}