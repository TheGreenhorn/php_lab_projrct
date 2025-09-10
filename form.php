<?php
session_start();

// Add field type
if (isset($_POST['add_field'])) {
    $_SESSION['fields'][] = $_POST['field_type'];
}

// Validate form
if (isset($_POST['submit_form'])) {
    $errors = [];
    $responses = [];

    foreach ($_SESSION['fields'] as $i => $field) {
        $value = $_POST["field_$i"] ?? '';

        if ($field == "text") {
            if ($value == "") {
                $errors[$i] = "Text cannot be empty!";
            }
        }

        if ($field == "email") {
            if ($value == "" || strpos($value, "@") === false) {
                $errors[$i] = "Enter a valid email (must contain @)";
            }
        }

        if ($field == "password") {
            if (strlen($value) < 6) {
                $errors[$i] = "Password must be at least 6 characters!";
            }
        }

        if ($field == "dropdown") {
            if ($value == "") {
                $errors[$i] = "Please select an option!";
            }
        }

        if ($field == "checkbox") {
            if ($value == "") {
                $errors[$i] = "You must check this box!";
            }
        }

        $responses["field_$i"] = $value;
    }

    // Save if no errors
    if (empty($errors)) {
        // Just save as plain text (simple for now)
        $line = "";
        foreach ($responses as $key => $val) {
            $line .= $key . ": " . $val . " | ";
        }
        file_put_contents("responses.txt", $line . PHP_EOL, FILE_APPEND);

        echo "<p style='color:green;'>✅ Form submitted successfully!</p>";
        session_destroy();
    } else {
        echo "<p style='color:red;'>⚠ Please fix the errors below.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Easy Dynamic Form</title>
</head>
<body>
    <h2>Dynamic Form Generator</h2>

    <!-- Step 1: Add Field -->
    <form method="post">
        <select name="field_type">
            <option value="text">Text</option>
            <option value="email">Email</option>
            <option value="password">Password</option>
            <option value="dropdown">Dropdown</option>
            <option value="checkbox">Checkbox</option>
        </select>
        <button type="submit" name="add_field">Add Field</button>
    </form>

    <hr>

    <!-- Step 2: Show Form -->
    <form method="post">
        <?php
        if (!empty($_SESSION['fields'])) {
            foreach ($_SESSION['fields'] as $i => $field) {
                echo "<label>Field $i ($field): </label>";

                if ($field == "text" || $field == "email" || $field == "password") {
                    echo "<input type='$field' name='field_$i'>";
                } elseif ($field == "dropdown") {
                    echo "<select name='field_$i'>
                            <option value=''>--Select--</option>
                            <option value='A'>Option A</option>
                            <option value='B'>Option B</option>
                          </select>";
                } elseif ($field == "checkbox") {
                    echo "<input type='checkbox' name='field_$i' value='yes'> Check me";
                }

                if (!empty($errors[$i])) {
                    echo "<span style='color:red;'> {$errors[$i]}</span>";
                }
                echo "<br><br>";
            }
            echo "<button type='submit' name='submit_form'>Submit</button>";
        }
        ?>
    </form>
</body>
</html>
