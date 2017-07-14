<?php

namespace Tests\Mock;


use App\Entity\User;

class SaveUserRequest extends AbstractRequest implements \App\Request\Contract\SaveUserRequest
{
    public function __construct(array $options, User $user = null)
    {
        parent::__construct(array_merge([
            'user' => $user
        ], $options));
    }

    /**
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->get('first_name');
    }

    /**
     * @return string|null
     */
    public function getLastName()
    {
        return $this->get('last_name');
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->get('is_active', true);
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->get('user', new User());
    }
}