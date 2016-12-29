<?php

    require_once('data.php');
    require_once('main.php');

    $options = set_options($options);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>renamer</title>
    <link rel="stylesheet" type="text/css" href="static/lib/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="static/style.css">
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
    <script type="text/javascript" src="static/lib/bootstrap.min.js"></script>
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
            render_debug();
        }
    ?>
</body>
</html>