<?php
declare(strict_types=1);

namespace App\Console\Util;

use Plexikon\Chronicle\Support\Contracts\Projector\ProjectorManager;
use Plexikon\Kernel\Provider\AccountServiceProvider;
use Plexikon\Kernel\Support\Console\ProjectionWorkerCommand;

final class ReadModelWorkersCommand extends ProjectionWorkerCommand
{
    public function __construct(ProjectorManager $projectorManager)
    {
        parent::__construct($projectorManager);

        $this->projections = AccountServiceProvider::READ_MODEL_COMMANDS;

        $this->projections += ['auth_account-stream' => 'read_model-auth_account'];
    }
}
