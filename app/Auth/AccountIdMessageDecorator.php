<?php
declare(strict_types=1);

namespace App\Auth;

use Illuminate\Support\Facades\Auth;
use Plexikon\Reporter\Contracts\Message\MessageDecorator;
use Plexikon\Reporter\Message\Message;

final class AccountIdMessageDecorator implements MessageDecorator
{
    public function decorate(Message $message): Message
    {
        // TODO: Implement decorate() method.
    }
}
