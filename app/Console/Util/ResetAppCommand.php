<?php
declare(strict_types=1);

namespace App\Console\Util;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Plexikon\Chronicle\Stream\Stream;
use Plexikon\Chronicle\Stream\StreamName;
use Plexikon\Chronicle\Support\Contracts\Chronicler\Chronicle;
use Plexikon\Kernel\Projection\Stream as KernelStream;

final class ResetAppCommand extends Command
{
    protected $signature = 'app:reset';

    private Connection $connection;
    private Chronicle $chronicle;

    private array $currentStreams = [KernelStream::ACCOUNT];

    public function __construct(Connection $connection, Chronicle $chronicle)
    {
        parent::__construct();
        $this->connection = $connection;
        $this->chronicle = $chronicle;
    }

    public function handle(): void
    {
//        if (!$this->confirm('Restart from scratch?')) {
//            $this->warn('Flush app aborted');
//        }

        $this->connection->getSchemaBuilder()->dropAllTables();

        $this->call('migrate');

        foreach ($this->currentStreams as $currentStream) {
            $this->chronicle->persistFirstCommit(new Stream(new StreamName($currentStream)));

            $this->line("Stream $currentStream created");
        }

        $this->info('Done .. setup your projections');
    }
}
