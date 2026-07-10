<?php

namespace BeyondSEODeps\Illuminate\Support\Facades;

use BeyondSEODeps\Illuminate\Support\Testing\Fakes\MailFake;

/**
 * @method static \Illuminate\Mail\Mailer mailer(string|null $name = null)
 * @method static void alwaysFrom(string $address, string|null $name = null)
 * @method static void alwaysReplyTo(string $address, string|null $name = null)
 * @method static void alwaysReturnPath(string $address)
 * @method static void alwaysTo(string $address, string|null $name = null)
 * @method static \Illuminate\Mail\PendingMail bcc($users)
 * @method static \Illuminate\Mail\PendingMail to($users)
 * @method static \BeyondSEODeps\Illuminate\Support\Collection queued(string $mailable, \Closure|string|null $callback = null)
 * @method static \BeyondSEODeps\Illuminate\Support\Collection sent(string $mailable, \Closure|string|null $callback = null)
 * @method static array failures()
 * @method static bool hasQueued(string $mailable)
 * @method static bool hasSent(string $mailable)
 * @method static mixed later(\DateTimeInterface|\DateInterval|int $delay, \BeyondSEODeps\Illuminate\Contracts\Mail\Mailable|string|array $view, ?string $queue = null)
 * @method static mixed laterOn(string $queue, \DateTimeInterface|\DateInterval|int $delay, \BeyondSEODeps\Illuminate\Contracts\Mail\Mailable|string|array $view)
 * @method static mixed queue(\BeyondSEODeps\Illuminate\Contracts\Mail\Mailable|string|array $view, ?string $queue = null)
 * @method static mixed queueOn(string $queue, \BeyondSEODeps\Illuminate\Contracts\Mail\Mailable|string|array $view)
 * @method static void assertNotQueued(string $mailable, ?callable $callback = null)
 * @method static void assertNotSent(string $mailable, callable|int|null $callback = null)
 * @method static void assertNothingQueued()
 * @method static void assertNothingSent()
 * @method static void assertQueued(string|\Closure $mailable, callable|int|null $callback = null)
 * @method static void assertSent(string|\Closure $mailable, callable|int|null $callback = null)
 * @method static void raw(string $text, $callback)
 * @method static void plain(string $view, array $data, $callback)
 * @method static void html(string $html, $callback)
 * @method static void send(\BeyondSEODeps\Illuminate\Contracts\Mail\Mailable|string|array $view, array $data = [], \Closure|string|null $callback = null)
 *
 * @see \Illuminate\Mail\Mailer
 * @see \BeyondSEODeps\Illuminate\Support\Testing\Fakes\MailFake
 */
class Mail extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @return \BeyondSEODeps\Illuminate\Support\Testing\Fakes\MailFake
     */
    public static function fake()
    {
        static::swap($fake = new MailFake);

        return $fake;
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mail.manager';
    }
}
