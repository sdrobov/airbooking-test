<?php

namespace App\Message;

use App\Entity\User;

final class SendEmailMessage
{
    /** @var User */
    private $user;

    /** @var string */
    private $message;

    /** @var string */
    private $subject;

    /**
     * SendEmailMessage constructor.
     * @param User $user
     * @param string $message
     * @param string $subject
     */
    public function __construct(User $user, string $message, string $subject)
    {
        $this->user = $user;
        $this->message = $message;
        $this->subject = $subject;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }
}
