<?php
declare(strict_types=1);

namespace App\Console\Util;

use Plexikon\Kernel\Provider\AccountServiceProvider;
use Plexikon\Kernel\Support\Console\SymfonyWorkerCommand;

final class ReadModelWorkersCommand extends SymfonyWorkerCommand
{
    protected array $readModels = AccountServiceProvider::READ_MODEL_COMMANDS;
}
