<?php

namespace App\Service;

use App\Helper\Exception\ApiException;
use App\Repository\UserRepository;

class UserService
{
    public function __construct(
        protected UserRepository              $userRepository,
    )
    {
    }

    public function checkEmailExist(string $email): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if($user){
            throw new ApiException(message: 'Уже есть пользователь с такой почтой');
        }
    }
}