<?php
/**
 * @author Adirelle <adirelle@gmail.com>
 *
 * Output PHP errors and exceptions in JSON.
 **/

namespace Peekmo\AtomAutocompletePhp;

class ErrorHandler
{
    static private $reserve;

    /**
     * Set up error handling.
     */
    public static function register()
    {
        // Keep some room in case of memory exhaustion
        self::$reserve = str_repeat(' ', 4096);

        // Ensure nothing will be send to stdout
        ini_set('display_errors', false);
        ini_set('log_errors', false);

        // Register our handlers
        register_shutdown_function('Peekmo\AtomAutocompletePhp\ErrorHandler::onShutdown');
        set_error_handler('Peekmo\AtomAutocompletePhp\ErrorHandler::onError');
        set_exception_handler('Peekmo\AtomAutocompletePhp\ErrorHandler::onException');
    }

    public static function onError($code, $message, $file, $line, $context)
    {
        // call onException directly instead of throw'ing
        // to work around https://bugs.php.net/bug.php?id=66216
        self::onException(new \ErrorException($message, $code, 1, $file, $line));
    }

    /**
     * Display uncaught exception in JSON.
     *
     * @param \Throwable $exception
     */
    public static function onException(\Throwable $exception)
    {
        die(
            json_encode(
                array('error' =>
                    array(
                        'message' => $exception->getMessage(),
                        'code' => $exception->getCode(),
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                    )
                )
            )
        );
    }

    /**
     * Display any fatal error in JSON.
     */
    public static function onShutdown()
    {
        // Do nothing
    }
}
