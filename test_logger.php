<?php
require_once __DIR__ . '/Core/libs/Logger.php';

echo "Testing Logger Class...\n";
Logger::Info("This is an informational message.");
Logger::Success("Operation completed successfully.");
Logger::Warning("This is a warning message.");
Logger::Error("An error occurred during process.");

echo "\nTesting Log Alias (Backward Compatibility)...\n";
Log::Info("Logging via Log alias works.");
Log::Success("Aliased success message.");
Log::Warning("Aliased warning message.");
Log::Error("Aliased error message.");

echo "\nVerification complete. Check colors in your terminal!\n";
?>
