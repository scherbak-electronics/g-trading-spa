<?php

namespace App\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Output\OutputInterface;

class ViewFactory
{
    public function create(Command $command, OutputInterface $output): View
    {
        return new View($command, $output);
    }
}
