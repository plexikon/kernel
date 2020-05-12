<?php
declare(strict_types=1);

namespace App\Console\App;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Plexikon\Kernel\Model\Account\Command\AccountChangeEmail;
use Plexikon\Kernel\Model\Account\Command\AccountChangeName;
use Plexikon\Kernel\Model\Account\Command\AccountChangePassword;
use Plexikon\Kernel\Model\Account\Command\MarkAccountAsEnabled;
use Plexikon\Kernel\Model\Account\Command\RegisterAccount;
use Plexikon\Kernel\Model\Account\Query\PaginateAccounts;
use Plexikon\Kernel\Projection\Customer\AccountModel;
use Plexikon\Reporter\Support\Publisher\LazyPublisher;

final class SeedAccountCommand extends Command
{
    protected $signature = 'app:seed-account {num=10 : Number of account to registered}';
    protected array $accountIds = [];
    protected string $defaultPassword = 'password123';
    protected LazyPublisher $publisher;
    protected Generator $faker;

    public function __construct(LazyPublisher $publisher)
    {
        parent::__construct();

        $this->publisher = $publisher;
        $this->faker = Factory::create();
    }

    public function handle(): void
    {
        $num = $count = (int)$this->argument('num') ?? 10;

        $registeredBar = $this->output->createProgressBar($count);
        $registeredBar->start();

        while ($num !== 0) {
            $this->registerAccount();
            $num--;
            $registeredBar->advance();
        }

        $registeredBar->finish();

        sleep(5);

        $updatedBar = $this->output->createProgressBar($count);
        $updatedBar->start();
        $this->paginatedAccounts($count)->each(function (AccountModel $model)use($updatedBar): void {
            $updatedBar->advance();
            $this->updateAccount($model->getId()->toString());
        });
        $updatedBar->finish();

        $this->info('Accounts registered and updated');
    }

    protected function registerAccount(): void
    {
        $this->accountIds[] = $accountId = $this->faker->uuid;

        $this->publisher->publishCommand(
            RegisterAccount::withData(
                $accountId,
                $this->faker->email,
                $this->safeName(),
                $this->defaultPassword,
                $this->defaultPassword
            )
        );

        $this->publisher->publishCommand(
            MarkAccountAsEnabled::withData($accountId)
        );
    }

    protected function updateAccount(string $accountId): void
    {
        $this->publisher->publishCommand(AccountChangeEmail::withData($accountId, $this->faker->email));

        $this->publisher->publishCommand(AccountChangeName::withData(
            $accountId, $this->safeName()
        ));

        $this->publisher->publishCommand(AccountChangePassword::withData(
            $accountId, $this->defaultPassword, 'password1234', 'password1234'
        ));

        $this->publisher->publishCommand(AccountChangePassword::withData(
            $accountId, 'password1234', $this->defaultPassword, $this->defaultPassword
        ));
    }

    protected function safeName(): string
    {
        $name = $this->faker->email;

        return explode('@', $name)[0];
    }

    protected function paginatedAccounts(int $limit = 10): Collection
    {
        return $this->publisher->publishQuery(
            new PaginateAccounts($limit)
        )->getCollection();
    }
}
