<?php

function extensionize($n, $opt) {
    /** Returns the name and extension <array>. */
    $ext_search = explode ('.', $n);

    if ($opt and count($ext_search) > 1) {
        $name = $ext_search[0];
        $ext = $ext_search[1];
    } else {
        $name = $ext_search[0];
        $ext = '';
    }

    $result = array(
        'name' => $name,
        'ext' => $ext
    );
    return $result;
}

function to_case($n, $case) {
    /** Returns the name as the specified case <string>. */
    if ($case == 'UPPERCASE') {
        $name = strtoupper($n);
    } else {
        $name = strtolower($n);
    }
    return $name;
}

function to_space_chars($n, $spc) {
    /** Returns the name with the specified
        space character substitution <string> */

    if ($spc == 'all') {
        $spc = '';
    }
    $name = str_replace(' ', $spc, $n);
    return $name;
}

function rm_numbers($n) {
    /** Returns the name without numbers <string> */
    $name = preg_replace('/[0-9]*/', '', $n);
    return $name;
}

function rm_other_chars($n) {
    /** Returns the name without other characters <string> */
    $name = preg_replace('/[~`!@#$%^&*()=+{}\[\]<>?\/.,;:"\'|]*/', '', $n);
    return $name;
}

function cleanup($n) {
    /** Checks for a dash or underscore at the
        beginning/end of the string. Returns the
        name withiout any those <string> */
    $name = $n;

    if ($n[0] == '_' or $n[0] == '-') {
        $name = substr($n, 1);
        if (substr($name, -1) == '_' or substr($name, -1) == '-') {
            $name = substr($name, 0, -1);
        }
    }
    return $name;
}

function renamer_init($n, $opts) {
    /** Takes in $n <string> and options <array>
        returns a short name <string> based on
        the options settings. */

    $name = trim($n);
    $ext = '';

    // handle an extension
    $extentioned = extensionize($name, $opts['inc_ext']);
    $name = $extentioned['name'];
    $ext = $extentioned['ext'];

    // convert case
    $case = $opts['case'];
    if ($case != 'none') {
        $name = to_case($name, $case);
    }

    // remove or replace spaces
    $spc = $opts['space_char'];
    if ($spc != 'none') {
        $name = to_space_chars($name, $spc);
    }

    // remove numbers
    if ($opts['rm_numbers']) {
        $name = rm_numbers($name);
    }

    // remove other characters, if required
    if ($opts['rm_special']) {
        $name = rm_other_chars($name);
    }

    // check for a dash or underscore
    // at the beginning/end and remove it
    $name = cleanup($name);

    // shorten to max length
    if (strlen($name) > $opts['max_length']) {
        $name = trim(substr($name, 0, $opts['max_length']));
    }

    // add the extension back in, if required
    if ($ext != '') {
        $name = $name . '.' . $ext;
    }

    return $name;
}

function set_options($options) {
    /** Sets user options
        Returns options <array> */

    // set the max length option and check for a number
    if (isset($_GET['max_length'])) {
        if (is_numeric($_GET['max_length'])) {
            $options['max_length'] = $_GET['max_length'];
        }
    }

    if (isset($_GET['inc_ext'])) {
        $options['inc_ext'] = true;
    }

    if (isset($_GET['rm_numbers'])) {
        $options['rm_numbers'] = true;
    }

    if (isset($_GET['rm_special'])) {
        $options['rm_special'] = true;
    }

    if (isset($_GET['space_char'])) {
        $options['space_char'] = $_GET['space_char'];
    }

    if (isset($_GET['case'])) {
        $options['case'] = $_GET['case'];
    }

    if (isset($_GET['debug'])) {
        $options['debug'] = true;
    }

    return $options;
}

function render_debug() {
    echo '<code>' . $entered_name . '<br>';
    if (isset($result)) {
        echo $result . '<br>';
    }
    print_r($options);
    echo '<br>';
    print_r($checked_options);
    echo '<br>';
    print_r($dropdown_options['case']);
    echo '<br>';
    print_r($dropdown_options['space_char']);
}