<?php

namespace Foxkit\Auth\Exception;

use Foxkit\Auth\UserInterface;

class AuthException extends \Exception
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * Get the user.
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the user.
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }
}
