<?php

namespace App\Broadcasting;

use App\Models\User;

class GameChannel
{
    public function join(User $user)
    {
        return true;
    }
}
