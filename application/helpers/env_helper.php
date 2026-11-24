<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function load_env() {
    $env_path = FCPATH . '.env';
    if (!file_exists($env_path)) {
        return;
    }
    
    $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            
            // Remove quotes if present
            if ($value !== '' && 
                (($value[0] === '"' && substr($value, -1) === '"') || 
                 ($value[0] === "'" && substr($value, -1) === "'"))) {
                $value = substr($value, 1, -1);
            }
            
            // Now, check if there are nested variables in the value
            // This is to handle cases like: KEY="VALUE1 KEY2=VALUE2"
            $matches = [];
            preg_match_all('/([A-Z][A-Z0-9_]{2,})=/', $value, $matches, PREG_OFFSET_CAPTURE);
            if (!empty($matches[1])) {
                $first_offset = (int)$matches[1][0][1];
                if ($first_offset > 0) {
                    $main_value = substr($value, 0, $first_offset);
                    $main_value = trim($main_value);
                    if ($main_value !== '' && 
                        (($main_value[0] === '"' && substr($main_value, -1) === '"') || 
                         ($main_value[0] === "'" && substr($main_value, -1) === "'"))) {
                        $main_value = substr($main_value, 1, -1);
                    }
                    putenv($key . '=' . $main_value);
                    $_ENV[$key] = $main_value;
                    
                    // Process nested variables
                    for ($i = 0; $i < count($matches[1]); $i++) {
                        $k2 = (string)$matches[1][$i][0];
                        $start = (int)$matches[1][$i][1];
                        $val_start = $start + strlen($k2) + 1;
                        $end = ($i + 1 < count($matches[1])) ? (int)$matches[1][$i + 1][1] : strlen($value);
                        $v2 = substr($value, $val_start, $end - $val_start);
                        $v2 = trim($v2);
                        if ($v2 !== '' && 
                            (($v2[0] === '"' && substr($v2, -1) === '"') || 
                             ($v2[0] === "'" && substr($v2, -1) === "'"))) {
                            $v2 = substr($v2, 1, -1);
                        }
                        putenv($k2 . '=' . $v2);
                        $_ENV[$k2] = $v2;
                    }
                    continue;
                }
            }
            
            // Default case - set the variable
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}
