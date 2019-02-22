<?php
/**
 * Gauname saugu patikrinta user input.
 * 
 * @param type $form
 * @return type
 */
function get_safe_input($form) {
    $filtro_parametrai = [
        'action' => FILTER_SANITIZE_SPECIAL_CHARS
    ];
    foreach ($form['fields'] as $field_id => $value) {
        $filtro_parametrai[$field_id] = FILTER_SANITIZE_SPECIAL_CHARS;
    }
    return filter_input_array(INPUT_POST, $filtro_parametrai);
}
/**
 * Patikriname ar formoje esancios validacijos funkcijos yra teisingos ir iskvieciame ju funkcijas(not empty, not a number).
 * 
 * @param type $safe_input
 * @param type $form
 * @return boolean
 * @throws Exception
 */
function validate_form($safe_input, &$form) {
    $success = true;
    foreach ($form['fields'] as $field_id => &$field) {
        foreach ($field['validate'] as $validator) {
            if (is_callable($validator)) {
                if (!$validator($safe_input[$field_id], $field, $safe_input)) {
                    $success = false;
                    break;
                }
            } else {
                throw new Exception(strtr('Not callable @validator function', [
                    '@validator' => $validator
                ]));
            }
        }
    }
    if ($success) {
        $form['validate'] = $form['validate'] ?? [];
        
		foreach ($form['validate'] as $validator) {
            if (is_callable($validator)) {
                if (!$validator($safe_input, $form)) {
                    $success = false;
                    break;
                }
            } else {
                throw new Exception(strtr('Not callable @validator function', [
                    '@validator' => $validator
                ]));
            }
        }
    }
    
    if ($success) {
        foreach ($form['callbacks']['success'] as $callback) {
            if (is_callable($callback)) {
                $callback($safe_input, $form);
            } else {
                throw new Exception(strtr('Not callable @function function', [
                    '@function' => $callback
                ]));
            }
        }
    } else {
        foreach ($form['callbacks']['fail'] as $callback) {
            if (is_callable($callback)) {
                $callback($safe_input, $form);
            } else {
                throw new Exception(strtr('Not callable @function function', [
                    '@function' => $callback
                ]));
            }
        }
    }
    
    return $success;
}
/**
 * Checks if field is empty
 * 
 * @param string $field_input
 * @param array $field $form Field
 * @return boolean
 */
function validate_not_empty($field_input, &$field, $safe_input) {
    if (strlen($field_input) == 0) {
        $field['error_msg'] = strtr('Jobans/a tu buhurs/gazele, '
                . 'kad palikai @field tuscia!', ['@field' => $field['label']
        ]);
    } else {
        return true;
    }
}
/**
 * Checks if field is a number
 * 
 * @param string $field_input
 * @param array $field $form Field
 * @return boolean
 */
function validate_is_number($field_input, &$field, $safe_input) {
    if (!is_numeric($field_input)) {
        $field['error_msg'] = strtr('Jobans/a tu buhurs/gazele, '
                . 'nes @field nera skaicius!', ['@field' => $field['label']
        ]);
    } else {
        return true;
    }
}