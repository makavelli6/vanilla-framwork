<?php
/**
 * Unified Logger Class
 * Supports colored CLI output and styled Browser output.
 * Inherits the Log alias for backward compatibility.
 */
class Logger
{
    /**
     * Log an Error message.
     */
    public static function Error(string $str): void
    {
        self::write($str, 'red', 'ERROR');
    }

    /**
     * Log a Success message.
     */
    public static function Success(string $str): void
    {
        self::write($str, 'green', 'SUCCESS');
    }

    /**
     * Log a Warning message.
     */
    public static function Warning(string $str): void
    {
        self::write($str, 'yellow', 'WARNING');
    }

    /**
     * Log an Informational message.
     */
    public static function Info(string $str): void
    {
        self::write($str, 'cyan', 'INFO');
    }

    /**
     * Core write function that detects environment and applies formatting.
     */
    private static function write(string $message, string $color, string $level): void
    {
        if (php_sapi_name() === 'cli') {
            echo self::formatCli($message, $color, $level) . PHP_EOL;
        } else {
            echo self::formatWeb($message, $color, $level) . "<br>";
        }
    }

    /**
     * Format output for CLI using ANSI escape codes.
     */
    private static function formatCli(string $message, string $color, string $level): string
    {
        $colors = [
            'red'    => "\033[31m",
            'green'  => "\033[32m",
            'yellow' => "\033[33m",
            'cyan'   => "\033[36m",
            'reset'  => "\033[0m"
        ];

        $c = $colors[$color] ?? $colors['reset'];
        $reset = $colors['reset'];

        return "[$c$level$reset] $message";
    }

    /**
     * Format output for Web using HTML/CSS.
     */
    private static function formatWeb(string $message, string $color, string $level): string 
    {
        $webColors = [
            'red'    => 'red',
            'green'  => 'greenyellow',
            'yellow' => 'gold',
            'cyan'   => 'cyan'
        ];

        $c = $webColors[$color] ?? 'white';
        $bg = ($color === 'green' || $color === 'cyan') ? 'background-color: black;' : '';

        return "<span style='color: $c; $bg font-weight: bold;'>[$level]</span> $message";
    }
}

/**
 * Log Alias for backward compatibility with legacy code.
 */
class Log extends Logger {}
?>
