<?php
require __DIR__ . '/vendor/autoload.php';
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class User {
    public $name;
    public $age;
}
class UserRegisteredEvent extends Event {
    const NAME = 'user.registered';
    protected $user;

    public function __construct(User $user) {
        $this->user = $user;
    }
    public function getUser() {
        return $this->user;
    }
}
class UserListener {
    public function onUserRegistrationAction(Event $event) {
        $user = $event->getUser();
        echo $user->name . "\r\n";
        echo $user->age . "\r\n";
    }
}
$user = new User();
$user->name = "Jon";
$user->age = 25;

$event = new UserRegisteredEvent($user);
$listener = new UserListener();

$dispatcher = new EventDispatcher();
$dispatcher
    ->addListener(
        UserRegisteredEvent::NAME,

        function(Event $event) {
            $user = $event->getUser();
            echo $user->name . "\r\n";
        });
$dispatcher
    ->addListener(
        UserRegisteredEvent::NAME, array($listener, 'onUserRegistrationAction'));

$dispatcher->dispatch(UserRegisteredEvent::NAME, $event);