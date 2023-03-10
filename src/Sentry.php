<?php

namespace Leeroy\SentryIO;

use Exception;
use JsonException;
use SailCMS\Collection;
use SailCMS\Database\Model;
use SailCMS\Sail;
use Sentry\State\Scope;
use SailCMS\Models\User;
use SailCMS\Http\Request;

use function Sentry\captureException;
use function Sentry\captureLastError;
use function Sentry\captureMessage;
use function Sentry\configureScope;
use function Sentry\init;
use function Sentry\withScope;

class Sentry
{
    /**
     *
     * Initialize sentry
     *
     * @param  array  $systemTags
     * @return void
     *
     */
    public static function init(array $systemTags = []): void
    {
        $env = env('ENVIRONMENT', 'dev');
        $environment = ($env === 'dev') ? 'development' : $env;

        init(['dsn' => env('SENTRY_DSN', ''), 'environment' => $environment]);

        // Set basic scope (sail version)
        configureScope(function (Scope $scope) use ($systemTags): void
        {
            $scope->setContext('sailcms', [
                'version' => Sail::SAIL_VERSION
            ]);

            if (!empty(User::$currentUser)) {
                $request = new Request();
                $scope->setUser(['id' => User::$currentUser->id, 'ip' => $request->ipAddress()]);
            }

            if (count($systemTags) > 0) {
                $finalTags = [];

                foreach ($systemTags as $tag => $value) {
                    $finalTags[env('SENTRY_ORG', 'sailcms') . '.' . $tag] = $value;
                }

                $scope->setTags($finalTags);
            }
        });
    }

    /**
     *
     * Manually capture an exception
     *
     * @param  Exception  $exception
     * @return void
     *
     */
    public static function capture(Exception $exception): void
    {
        captureException($exception);
    }

    /**
     *
     * Capture an error with a given context, tags and custom message
     *
     * @param  Exception               $exception
     * @param  string                  $contextName
     * @param  array|Collection|Model  $context
     * @param  array                   $tags
     * @param  string                  $message
     * @return void
     * @throws JsonException
     *
     */
    public static function captureWithContext(
        Exception $exception,
        string $contextName,
        array|Collection|Model $context = [],
        array $tags = [],
        string $message = ''
    ): void {
        if (!is_array($context)) {
            if (get_class($context) === Collection::class) {
                $context = $context->unwrap();
            } else {
                $context = $context->toJSON(true);
            }
        }

        withScope(function (Scope $scope) use ($exception, $message, $contextName, $context, $tags): void
        {
            $scope->setContext($contextName, $context);

            $finalTags = [];

            foreach ($tags as $tag => $value) {
                $finalTags[env('SENTRY_ORG', 'sailcms') . '.' . $tag] = $value;
            }

            $scope->setTags($finalTags);

            if ($message !== '') {
                captureMessage($message);
                return;
            }

            captureException($exception);
        });
    }

    /**
     *
     * Capture the last error that happened
     *
     * @return void
     *
     */
    public static function captureLastError(): void
    {
        captureLastError();
    }
}