<?php

/**
 * Custom exception for SoapCurlClient errors.
 *
 * This class extends the built-in Exception class to provide a custom
 * exception for handling errors that occur in the SoapCurlClient class.
 */
class SoapCurlClientException extends Exception
{
    /**
     * Constructor to initialize the exception with a custom message and code.
     *
     * @param string $message The error message.
     * @param int $code The error code (optional).
     * @param Exception|null $previous The previous exception used for exception chaining (optional).
     */
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        // Pass custom message and code to the base class
        parent::__construct($message, $code, $previous);
    }

    /**
     * String representation of the exception for logging or debugging purposes.
     *
     * @return string A detailed error message with the exception message, code, and trace.
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\nStack trace:\n{$this->getTraceAsString()}";
    }
}
