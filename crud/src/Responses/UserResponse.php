<?php

namespace App\Responses;

use App\Entity\User;

class UserResponse
{
    /** @var int */
    public $id = 0;

    /** @var string */
    public $name = '';

    /** @var string */
    public $email = '';

    /** @var bool */
    public $isAdmin = false;
}
