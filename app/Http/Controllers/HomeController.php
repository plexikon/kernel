<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Collection;
use Plexikon\Kernel\Model\Account\Command\AccountChangeEmail;
use Plexikon\Kernel\Model\Account\Command\AccountChangeName;
use Plexikon\Kernel\Model\Account\Command\AccountChangePassword;
use Plexikon\Kernel\Model\Account\Command\MarkAccountAsEnabled;
use Plexikon\Kernel\Model\Account\Command\RegisterAccount;
use Plexikon\Kernel\Model\Account\Query\PaginateAccounts;
use Plexikon\Kernel\Projection\Customer\AccountModel;
use Plexikon\Reporter\CommandPublisher;
use Plexikon\Reporter\QueryPublisher;
use Plexikon\Reporter\Support\HasPromiseHandler;

final class HomeController
{
    use HasPromiseHandler;

    private string $currentPassword = 'password123';

    public function __invoke(CommandPublisher $publisher, QueryPublisher $queryPublisher, Factory $factory)
    {
        $faker = Factory::create();
//        $accounts = $this->getAccounts();
//        $accounts->each(function (AccountModel $model) use ($publisher, $faker) {
//            $this->updateAccount($publisher, $faker, $model->getId()->getValue());
//        });

//        return $accounts;
        $start = microtime(true);

        $i = 20;
        $ids = [];

        while ($i !== 0) {
            $ids [] = $accountId = $faker->uuid;
            $this->seed($publisher, $faker, $accountId);
            $i--;
        }

        foreach ($ids as $id) {
            $this->enableAccount($publisher, $id);
        }


//        foreach ($ids as $id) {
//            $this->updateAccount($publisher, $faker, $id);
//        }

        return ('elapsed time: ' . (microtime(true) - $start));
    }

    private function seed(CommandPublisher $publisher, Generator $faker, string $accountId): void
    {
        $publisher->dispatch(
            RegisterAccount::withData(
                $accountId,
                $faker->email,
                $this->getSafeFirstName($faker),
                $this->currentPassword,
                $this->currentPassword,
            )
        );
    }

    private function enableAccount(CommandPublisher $publisher, string $accountId): void
    {
        $publisher->dispatch(MarkAccountAsEnabled::withData($accountId));
    }

    private function updateAccount(CommandPublisher $publisher, Generator $faker, string $accountId): void
    {
        $publisher->dispatch(AccountChangeEmail::withData($accountId, $faker->email));

        $publisher->dispatch(AccountChangeName::withData(
            $accountId, $this->getSafeFirstName($faker)
        ));

        $publisher->dispatch(AccountChangePassword::withData(
            $accountId, $this->currentPassword, 'password1234', 'password1234'
        ));

        $publisher->dispatch(AccountChangePassword::withData(
            $accountId, 'password1234', $this->currentPassword, $this->currentPassword
        ));
    }

    private function getAccounts(): Collection
    {
        return $this->handlePromise(
            app(QueryPublisher::class)->dispatch(
                new PaginateAccounts(100)
            )
        )->getCollection();
    }

    private function getSafeFirstName(Generator $faker): string
    {
        $name = $faker->firstName;

        if (strlen($name) < 5) {
            $name .= '-foo';
        }

        return $name;
    }
}
