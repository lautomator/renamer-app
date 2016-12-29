<?php

// options (default)
$options = array(
    'max_length' => 16,
    'inc_ext' => false,
    'space_char' => '_',
    'case' => 'lowercase',
    'rm_numbers' => false,
    'rm_special' => false,
    'debug' => false
);

$entered_name = '';

$checked_options = array(
    'inc_ext' => '',
    'rm_numbers' => '',
    'rm_special' => '',
    'debug' => ''
);

$dropdown_options = array(
    'case' => array(
        'lowercase' => '',
        'UPPERCASE' => '',
        'none' => ''
    ),
    'space_char' => array(
        '_' => '',
        '-' => '',
        'none' => '',
        'all' => ''
    ),
);