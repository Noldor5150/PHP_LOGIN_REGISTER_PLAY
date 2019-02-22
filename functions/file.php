<?php
/**
 * Funkcija ideda array i faila.
 * @param type $array
 * @param type $file
 * @return type
 */
function array_to_file($array, $file) {
    $json_array = json_encode($array);
    return file_put_contents($file, $json_array);
}
/**
 * Funkcija nuskaito faila ir iskelia is failo i array
 * @param type $file
 * @return type
 * @throws Exception
 */
function file_to_array($file) {
    if (file_exists($file)) {
        $string = file_get_contents($file);
        if ($string !== false) {
            return json_decode($string, true);
        } else {
            throw new Exception(strtr('@file not readable', [
                '@file' => $file
            ]));
        }
    } else {
        throw new Exception(strtr(' @file not exists', [
            '@file' => $file
        ]));
    }
}