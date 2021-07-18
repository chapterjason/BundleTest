<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\BundleTest;

use PHPUnit\Framework\Constraint\LogicalNot;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Event\MessageEvents;
use Symfony\Component\Mailer\Test\Constraint as MailerConstraint;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Test\Constraint as MimeConstraint;

trait MailerAssertionsTrait
{
    public function assertEmailCount(int $count, string $transport = null, string $message = ''): void
    {
        self::assertThat($this->getMessageMailerEvents(), new MailerConstraint\EmailCount($count, $transport), $message);
    }

    public function assertQueuedEmailCount(int $count, string $transport = null, string $message = ''): void
    {
        self::assertThat($this->getMessageMailerEvents(), new MailerConstraint\EmailCount($count, $transport, true), $message);
    }

    public function assertEmailIsQueued(MessageEvent $event, string $message = ''): void
    {
        self::assertThat($event, new MailerConstraint\EmailIsQueued(), $message);
    }

    public function assertEmailIsNotQueued(MessageEvent $event, string $message = ''): void
    {
        self::assertThat($event, new LogicalNot(new MailerConstraint\EmailIsQueued()), $message);
    }

    public function assertEmailAttachmentCount(RawMessage $email, int $count, string $message = ''): void
    {
        self::assertThat($email, new MimeConstraint\EmailAttachmentCount($count), $message);
    }

    public function assertEmailTextBodyContains(RawMessage $email, string $text, string $message = ''): void
    {
        self::assertThat($email, new MimeConstraint\EmailTextBodyContains($text), $message);
    }

    public function assertEmailTextBodyNotContains(RawMessage $email, string $text, string $message = ''): void
    {
        self::assertThat($email, new LogicalNot(new MimeConstraint\EmailTextBodyContains($text)), $message);
    }

    public function assertEmailHtmlBodyContains(RawMessage $email, string $text, string $message = ''): void
    {
        self::assertThat($email, new MimeConstraint\EmailHtmlBodyContains($text), $message);
    }

    public function assertEmailHtmlBodyNotContains(RawMessage $email, string $text, string $message = ''): void
    {
        self::assertThat($email, new LogicalNot(new MimeConstraint\EmailHtmlBodyContains($text)), $message);
    }

    public function assertEmailHasHeader(RawMessage $email, string $headerName, string $message = ''): void
    {
        self::assertThat($email, new MimeConstraint\EmailHasHeader($headerName), $message);
    }

    public function assertEmailNotHasHeader(RawMessage $email, string $headerName, string $message = ''): void
    {
        self::assertThat($email, new LogicalNot(new MimeConstraint\EmailHasHeader($headerName)), $message);
    }

    public function assertEmailHeaderSame(RawMessage $email, string $headerName, string $expectedValue, string $message = ''): void
    {
        self::assertThat($email, new MimeConstraint\EmailHeaderSame($headerName, $expectedValue), $message);
    }

    public function assertEmailHeaderNotSame(RawMessage $email, string $headerName, string $expectedValue, string $message = ''): void
    {
        self::assertThat($email, new LogicalNot(new MimeConstraint\EmailHeaderSame($headerName, $expectedValue)), $message);
    }

    public function assertEmailAddressContains(RawMessage $email, string $headerName, string $expectedValue, string $message = ''): void
    {
        self::assertThat($email, new MimeConstraint\EmailAddressContains($headerName, $expectedValue), $message);
    }

    /**
     * @return MessageEvents[]
     */
    public function getMailerEvents(string $transport = null): array
    {
        return $this->getMessageMailerEvents()->getEvents($transport);
    }

    public function getMailerEvent(int $index = 0, string $transport = null): ?MessageEvent
    {
        return $this->getMailerEvents($transport)[$index] ?? null;
    }

    /**
     * @return RawMessage[]
     */
    public function getMailerMessages(string $transport = null): array
    {
        return $this->getMessageMailerEvents()->getMessages($transport);
    }

    public function getMailerMessage(int $index = 0, string $transport = null): ?RawMessage
    {
        return $this->getMailerMessages($transport)[$index] ?? null;
    }

    private function getMessageMailerEvents(): MessageEvents
    {
        $container = $this->getContainer();
        if ($container->has('mailer.message_logger_listener')) {
            return $container->get('mailer.message_logger_listener')->getEvents();
        }

        if ($container->has('mailer.logger_message_listener')) {
            return $container->get('mailer.logger_message_listener')->getEvents();
        }

        static::fail('A client must have Mailer enabled to make email assertions. Did you forget to require symfony/mailer?');
    }
}
