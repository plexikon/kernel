<?php
declare(strict_types=1);

namespace App\Auth;

use Illuminate\Console\Command;
use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Chronicle\Support\Contracts\Projector\ProjectorManager;
use Plexikon\Chronicle\Support\Contracts\Projector\ReadModel;
use Plexikon\Kernel\Projection\Stream;
use Plexikon\Reporter\Message\Message;

/**
 * @method ReadModel readModel()
 */
final class AuthReadModelProjection extends Command
{
    protected $signature = 'kernel:read_model-auth_account';

    private ProjectorManager $projectorManager;
    private AuthReadModel $readModel;

    public function __construct(ProjectorManager $projectorManager, AuthReadModel $readModel)
    {
        parent::__construct();

        $this->projectorManager = $projectorManager;
        $this->readModel = $readModel;
    }

    public function handle(): void
    {
        pcntl_async_signals(true);

        $projection = $this->projectorManager->createReadModelProjection('auth_account-stream', $this->readModel);

        pcntl_signal(SIGINT, function () use ($projection): void {
            $projection->stop();
        });

        $projection
            ->withQueryFilter($this->projectorManager->projectionQueryFilter())
            ->fromStreams(Stream::ACCOUNT)
            ->whenAny(function (array $state, AggregateChanged $event): void {
                $this->readModel()->stack('auth ...', $event);
            })
            ->run(true);
    }
}
