<?php

namespace Foxkit\User\Event;

use Foxkit\Application as App;
use Foxkit\Auth\Event\LoginEvent;
use Foxkit\Event\EventSubscriberInterface;
use Foxkit\User\Model\User;

class UserListener implements EventSubscriberInterface
{
    /**
     * Updates user's last login time
     */
    public function onUserLogin(LoginEvent $event)
    {
        User::updateLogin($event->getUser());
    }

    public function onRoleDelete($event, $role)
    {
        User::removeRole($role);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'auth.login' => 'onUserLogin',
            'model.role.deleted' => 'onRoleDelete'
        ];
    }
}
