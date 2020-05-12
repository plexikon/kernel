<?php

use App\Resource\AccountsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Plexikon\Kernel\Model\Account\Command\RegisterAccount;
use Plexikon\Kernel\Model\Account\Query\GetAccountById;
use Plexikon\Kernel\Model\Account\Query\PaginateAccounts;
use Plexikon\Reporter\CommandPublisher;
use Plexikon\Reporter\QueryPublisher;
use React\Promise\PromiseInterface;

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('account', function (QueryPublisher $publisher) {
    $result = handlePromise(
        $publisher->dispatch(new PaginateAccounts())
    );

    return (new AccountsResource($result));
});

Route::get('account/{accountId}', function (QueryPublisher $publisher, Request $request) {
    $result = handlePromise(
        $publisher->dispatch(
            new GetAccountById(
                $request->route()->parameter('accountId')
            )
        )
    );

    return (new AccountsResource($result));
});

Route::post('account', function (Request $request, CommandPublisher $publisher) {
    $payload = $request->json()->all();

    $command = RegisterAccount::fromPayload($payload);

    $publisher->dispatch($command);

    return new \Illuminate\Http\JsonResponse();
});


function handlePromise(PromiseInterface $promise)
{
    $exception = null;
    $result = null;

    $promise->then(
        static function ($data) use (&$result) {
            $result = $data;
        },
        static function ($exc) use (&$exception) {
            $exception = $exc;
        }
    );

    if ($exception instanceof Throwable) {
        throw $exception;
    }

    return $exception ?? $result;
}
