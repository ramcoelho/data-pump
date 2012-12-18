<?php
/**
 * runDataPump
 *
 * Script for data migration
 * Your ruleset file SHOULD NOT perform any changes on the origin
 * database and SHOULD setup the destination database to avoid
 * duplication of data, even if ran repeteadly.
 *
 * You MUST NOT change this file.
 * Configure this script on ConnectionBootstrap.php file.
 *
 * @package   NexyUtilities
 * @author    "Ricardo Coelho" <ricardo@nexy.com.br>
 * @copyright 2012 © Nexy Serviços de Informática Ltda.
 * @category  Data
 * @license   GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 * @link      http://cdn.nexy.com.br/packages/data/run-data-pump.php.txt
 * 
 */

require 'lib/DataPump.php';
require 'config/ConnectionBootstrap.php';

echo "
I'm pretty sure you are careful, and so, but just for the records... 

1. You DID remember to backup your destination database, right?

2 . You are sure your migration ruleset won't touch the origin
    database at all, will it?

3. If you HAVE to change the origin database, you did backup
   it too, am I correct?

   Well, that said, are you sure you want to proceed, risking
   to LOOSE ALL YOUR DATA? (y/N) ";

$c = fread(STDIN, 1);
if ('y' != strtolower($c)) {
    die("\nOk. I'll wait for the backup to finish, no problem. " . 
        "Call me back whenever you're ready.\n\n"
    );
}

try {
    $conn_origin = ConnectionBootstrap::getOriginConnection();
} catch (Exception $e) {
    die('Unable to connect on origin server. ' . 
        $e->getMessage() . 
        "\n"
    );
}

try {
    $conn_destination = ConnectionBootstrap::getDestinationConnection();
} catch (Exception $e) {
    die('Unable to connect on destination server. ' . 
        $e->getMessage() . 
        "\n"
    );
}

try {
    $ruleset = ConnectionBootstrap::getRuleSet();
} catch (Exception $e) {
    die('Unable to load ruleset. ' . 
        $e->getMessage() . 
        "\n"
    );
}

$migrationResult = DataPump::migrate(
    $conn_origin, 
    $conn_destination, 
    $ruleset
);

if ($migrationResult->is_error) {
    $errors = implode("\n", $migrationResult->error_messages);
    die('Errors ocurred on data migration: ' . $errors . "\n");
}

echo "Data migration succeeded.\n";
