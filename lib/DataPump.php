<?php
/**
 * DataPump
 *
 * Migrate data between two PDO databases using a Markdown ruleset
 *
 * @package   NexyUtilities
 * @author    "Ricardo Coelho" <ricardo@nexy.com.br>
 * @copyright 2012 © Nexy Serviços de Informática Ltda.
 * @category  Data
 * @license   GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 * @link      http://cdn.nexy.com.br/packages/data/data-pump.php.txt
 * 
 */

class DataPump
{
    /**
     * Store migration errors
     * 
     */
    static protected $errors;

    /**
     * Store migration generic parameters
     * 
     */
    static protected $parameters;

    /**
     * Store migration table parameters
     * 
     */
    static protected $table_parameters;

    /**
     * Register a migration parameter. Used during parsing
     * 
     * @return Array Migration parameters
     */
    static public function registerParameter($table, $variable, $value)
    {
        if (!empty($variable)
            && !empty($value)) {
            if (empty($table)) {
                self::$parameters[$variable] = implode(' ', $value);
            } else {
                if (!isset(self::$table_parameters[$table])) {
                    self::$table_parameters[$table] = array();
                }
                self::$table_parameters[$table][$variable] = implode(' ', $value);
            }
        }
    }

    /**
     * Parse a ruleset file into array of migration parameters
     * 
     * @return Array Migration parameters
     */
    static public function parse($ruleset)
    {
        $table = '';
        $variable = '';
        $indented_lines = array();
        self::$parameters = array();

        foreach ($ruleset as $line) {
            if (preg_match('/\{([^\}]+)\}/', $line, $matches)) {
                self::registerParameter($table, $variable, $indented_lines);
                $table = $matches[1];
                $variable = '';
                $indented_lines = array();
            }
            if (preg_match('/\[([^]]+)\]/', $line, $matches)) {
                self::registerParameter($table, $variable, $indented_lines);
                $variable = $matches[1];
                $indented_lines = array();
            }
            if (preg_match('/^(\t|    )(.*)$/', $line, $matches)) {
                $indented_lines[] = trim($matches[2]);
            }
        }
        self::registerParameter($table, $variable, $indented_lines);
    }    
    /**
     * Migrate data
     * 
     * @return StdObject Migration status (obj->is_error, obj->error_messages)
     */
    static public function migrate($origin, $destination, $ruleset)
    {
        self::$errors = array();
        self::parse($ruleset);

        try {
            $origin->exec(self::$parameters['o-set']);
        } catch (Exception $e) {
            self::$errors[] = $e->getMessage();
            return false;
        }

        try {
            $destination->exec(self::$parameters['d-set']);
        } catch (Exception $e) {
            self::$errors[] = $e->getMessage();
            return false;
        }

        foreach (self::$table_parameters as $table => $parameters) {
            if (isset($parameters['t-set'])) {
                try {
                    $destination->exec($parameters['t-set']);
                } catch (Exception $e) {
                    self::$errors[] = $e->getMessage();
                    continue;
                }                
            }
            if (isset($parameters['rst'])) {
                try {
                    $destination->exec($parameters['rst']);
                } catch (Exception $e) {
                    self::$errors[] = $e->getMessage();
                    continue;
                }                
            }
            if (isset($parameters['rst'])) {
                try {
                    $destination->exec($parameters['rst']);
                } catch (Exception $e) {
                    self::$errors[] = $e->getMessage();
                    continue;
                }                
            }
            if (isset($parameters['t-tear'])) {
                try {
                    $destination->exec($parameters['t-tear']);
                } catch (Exception $e) {
                    self::$errors[] = $e->getMessage();
                    continue;
                }                
            }
        }

        try {
            $destination->exec(self::$parameters['d-tear']);
        } catch (Exception $e) {
            self::$errors[] = $e->getMessage();
        }

        try {
            $origin->exec(self::$parameters['o-tear']);
        } catch (Exception $e) {
            self::$errors[] = $e->getMessage();
        }

        return (object) array(
            'is_error' => (0 != sizeof(self::$errors)),
            'error_messages' => self::$errors
        );
    }
}