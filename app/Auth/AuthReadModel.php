<?php
declare(strict_types=1);

namespace App\Auth;

use Illuminate\Database\Schema\Blueprint;
use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Chronicle\Support\ReadModel\ConnectionReadModel;
use Plexikon\Chronicle\Support\ReadModel\HasConnectionOperation;
use Plexikon\Kernel\Model\Account\Event\AccountRegistered;
use Plexikon\Reporter\Message\Message;

final class AuthReadModel extends ConnectionReadModel
{
    use HasConnectionOperation;

    /**
     * @var AggregateChanged[]
     */
    private array $messages = [];

    public function stack(string $operation, ...$events): void
    {
        $this->messages [] = $events[0];
    }

    public function persist(): void
    {
        foreach ($this->messages as $event) {
            if ($event instanceof AccountRegistered) {
                $this->insert([
                    'id' => $event->aggregateRootId(),
                    'email' => $event->email()->getValue(),
                    'name' => $event->name()->getValue(),
                    'password' => $event->password()->getValue()
                ]);
            }
        }

        $this->messages = [];
    }

    protected function up(): callable
    {
        return function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        };
    }

    protected function tableName(): string
    {
        return 'users';
    }
}
