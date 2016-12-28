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

    function set_options() {
        /** Sets user options
            Returns options <array> */

        // defaults
        $options = array(
            'max_length' => 16,
            'inc_ext' => false,
            'space_char' => '_',
            'case' => 'lowercase',
            'rm_numbers' => false,
            'rm_special' => false,
            'debug' => false
        );

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

    $options = set_options();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>renamer</title>
    <link href="lib/bootstrap.min.css" rel="stylesheet">

    <style type="text/css">
        .container {
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5%;
            max-width: 300px;
            padding-bottom: 15px;
        }

        input {
            margin: 5px 0;
        }

        label {
            margin-bottom: 0;
        }

        input[type="checkbox"] {
            cursor: pointer;
            margin: 0;
        }

        select {
            cursor: pointer;
        }

        .cell_l {
            width: 25px;
        }

        .console {
            background-color: #333;
            border-radius: 6px;
            color: cyan;
            font: 12px/16px 'Monaco', monospace;
            padding: 10px;
            word-wrap: break-word;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <!-- IN -->
            <div class="col-md-12">
                <h1>Renamer</h1>
                <hr>
                <h3>Options</h3>
                <form method="GET" action="index.php">
                <?php
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

                    // update the name
                    if (isset($_GET['n'])) {
                        $entered_name = trim($_GET['n']);
                    }

                    // update the checkboxes
                    foreach ($checked_options as $opt_key => $opt_val) {
                        if ($options[$opt_key] == '1') {
                            $checked_options[$opt_key] = 'checked';
                        }
                    }

                    // update the dropdown menus
                    // cases
                    foreach ($dropdown_options['case'] as $opt_key => $opt_val) {
                        if ($opt_key == $options['case']) {
                            $dropdown_options['case'][$opt_key] = 'selected';
                        }
                    }

                    // spaces
                    foreach ($dropdown_options['space_char'] as $opt_key => $opt_val) {
                        if ($opt_key == $options['space_char']) {
                            $dropdown_options['space_char'][$opt_key] = 'selected';
                        }
                    }

                ?>
                    <input class="form-control text_option" type="text" name="n" placeholder="Name" value="<?php echo $entered_name; ?>" autofocus required>

                    <label class="text-muted">Length <em>(default: 16, min: 1, max: 72)</em></label>
                    <input class="form-control text_option" type="number" name="max_length" min="1" max="72" value="<?php echo $options['max_length']; ?>">

                    <table class="table-condensed">
                        <tr>
                            <td class="cell_l"><input class="form-control check_option" type="checkbox" <?php echo $checked_options['inc_ext']; ?> name="inc_ext"></td>
                            <td><label class="text-muted">Include Extension</label></td>
                        </tr>
                        <tr>
                            <td class="cell_l"><input class="form-control check_option" type="checkbox" <?php echo $checked_options['rm_numbers']; ?> name="rm_numbers"></td>
                            <td><label class="text-muted">Remove Numbers</label></td>
                        </tr>
                        <tr>
                            <td class="cell_l"><input class="form-control check_option" type="checkbox" <?php echo $checked_options['rm_special']; ?> name="rm_special"></td>
                            <td><label class="text-muted">Remove Other Characters</label></td>
                        </tr>
                        <tr>
                            <td class="cell_l"><input class="form-control check_option" type="checkbox" <?php echo $checked_options['debug']; ?> name="debug"></td>
                            <td><label class="text-muted">Debug</label></td>
                        </tr>
                    </table>

                    <label class="text-muted">Case</label>
                    <select class="form-control" name="case">
                        <option <?php echo $dropdown_options['case']['lowercase']?> value="lowercase">lowercase</option>
                        <option <?php echo $dropdown_options['case']['UPPERCASE']?> value="UPPERCASE">UPPERCASE</option>
                        <option <?php echo $dropdown_options['case']['none']?> value="none">none</option>
                    </select>

                    <label class="text-muted">Space Substitution</label>
                    <select class="form-control" name="space_char">
                        <option <?php echo $dropdown_options['space_char']['_']?> value="_">_</option>
                        <option <?php echo $dropdown_options['space_char']['-']?> value="-">-</option>
                        <option <?php echo $dropdown_options['space_char']['none']?> value="none">leave spaces</option>
                        <option <?php echo $dropdown_options['space_char']['all']?> value="all">remove all spaces</option>
                    </select>

                    <input class="form-control btn-primary" type="submit" value="Rename it!">
                    <input class="form-control btn-default" id="reset" type="reset" value="reset">
                </form>

            </div>
        </div><!-- /row -->

    <?php if (isset($_GET['n']) && $_GET['n'] != ''): ?>
        <div class="row">
            <!-- OUT -->
            <div class="col-md-12">
                <h3>Output</h3>
                <!-- results -->
                <div class="list-unstyled console">
                <?php
                    $result = renamer_init($_GET['n'], $options);
                    echo $result;
                ?>
                </div>
            </div>
        </div><!-- /row -->
    <?php endif; ?>
    </div><!-- /container -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript" src="lib/bootstrap.min.js"></script>
    <script type="text/javascript">
        (function($) {
            // clear the form contents
            $('#reset').click(function() {
                window.open('index.php', '_self');
            });
        })(jQuery);
    </script>

    <?php
        if ($options['debug']) {
            echo '<code>';

            echo $entered_name;
            echo '<br>';
            if (isset($result)) {
                echo $result;
            }
            echo '<br>';
            print_r($options);
            echo '<br>';
            print_r($checked_options);
            echo '<br>';
            print_r($dropdown_options['case']);
            echo '<br>';
            print_r($dropdown_options['space_char']);
        }
    ?>
</body>
</html>