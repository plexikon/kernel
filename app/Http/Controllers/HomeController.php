<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Plexikon\Kernel\Model\Account\Command\MarkAccountAsEnabled;
use Plexikon\Kernel\Model\Account\Command\RegisterAccount;
use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Kernel\Projection\Customer\AccountModel;
use Plexikon\Reporter\CommandPublisher;

final class HomeController
{
    public function __invoke(CommandPublisher $publisher)
    {
//        $publisher->dispatch(MarkAccountAsEnabled::withData('869cd82b-b925-4e70-8eb3-f3ebc9f61045'));
//
//        dd(AccountModel::where('email', 'plexikon@gmail.com')->first()->toArray());

       // $this->registerMe();
        // $this->loginMe();
        // Auth::logout();
        dump(Auth::user());

        return 'home';
    }

    private function registerMe(): void
    {
        $publisher = app(CommandPublisher::class);

        $publisher->dispatch(RegisterAccount::withData(
            $accountId = AccountId::create()->toString(),
            'plexikon@gmail.com',
            'plexikon',
            "password123",
            "password1234",
        ));

        $publisher->dispatch(MarkAccountAsEnabled::withData($accountId));
    }

    public function loginMe(): void
    {
        $user = User::where('email', 'plexikon@gmail.com')->first();

        Auth::login($user);
    }
}
