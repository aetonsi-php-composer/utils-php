<?php

namespace Aetonsi\Utils;


class PHP
{
    use \Aetonsi\Traits\CompatibilityTrait;

    const ERROR_HANDLER_METHOD_NAME = 'error_handler';

    /**
     * Default error handler. Throws an \ErrorException for any uncaught error included in \error_reporting().
     *
     * @throws \ErrorException
     * @return bool
     */
    public static function error_handler($severity, $message, $file, $line)
    {
        if (!(\error_reporting() & $severity)) {
            // This error code is not included in error_reporting
            return false;
        }

        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * Shows every error.
     *
     * @see https://www.php.net/manual/en/function.error-reporting.php
     */
    public static function showAllErrors()
    {
        \ini_set('display_errors', '1');
        \ini_set('display_startup_errors', '1');
        \error_reporting(\E_ALL);
    }

    /**
     * Intercepts all errors of all levels and converts them to ErrorExceptions.
     * Please note: E_PARSE errors are intercepted only if the file with the syntax error is included AFTER calling this function.
     *
     * @see https://www.php.net/manual/en/function.set-error-handler.php
     * @see https://www.php.net/manual/en/class.errorexception.php
     *
     * @param callable|null $handler
     */
    public static function setErrorHandler($handler = null)
    {
        return \set_error_handler($handler);
    }

    /**
     * Restores the previous error handler.
     */
    public static function unsetErrorHandler()
    {
        return self::setErrorHandler(null);
    }

    /**
     * Sets the default error handler.
     * @see self::error_handler()
     */
    public static function convertAllErrorsToExceptions()
    {
        // using self::fqcn() instead of self::class for compatibility
        return self::setErrorHandler([self::fqcn(), self::ERROR_HANDLER_METHOD_NAME]);
    }

    /**
     * Sets the default error handling settings (show all errors, convert all errors to exceptions).
     *
     * @see self::showAllErrors()
     * @see self::convertAllErrorsToExceptions()
     */
    public static function setDefaultErrorHandlingSettings()
    {
        self::showAllErrors();
        self::convertAllErrorsToExceptions();
    }

    /**
     * Runs the given function capturing its output and returning it as string.
     *
     * @param callable|\Closure $fn
     * @return string
     */
    public static function catchOutput($fn)
    {
        $args = [];
        for ($i = 1; $i < \func_num_args(); $i++) { // skip first argument as it's $fn
            $args[] = \func_get_args()[$i];
        }
        \ob_start();
        \call_user_func_array($fn, $args);
        return \ob_get_clean();
    }
}
