<?php
include_once 'classes/Enum.php';
include_once 'classes/PermissionFlag.php';

$permissionsList = PermissionFlag::toArray();

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    if ($_POST['type'] == "reversePermission") {
        if (isset($_POST['permissionFlags'])) {
            foreach ($permissionsList as $name => $value) {
//                var_dump($name, $value, $_POST['permissionFlags'], $_POST['permissionFlags'], $_POST['permissionFlags']&$value);
                echo "<p> " . $name . ": " . ((bool)($_POST['permissionFlags']&$value)?"True":"False"). "</p>";
            }
        }
    }
    if ($_POST['type'] == "permission") {
        $permission = 0;

        foreach ($permissionsList as $name => $value) {
            if (filter_has_var(INPUT_POST, $name)) $permission += $value;
        }

        echo "<p> Permission: ".$permission." </p>";
    }
    if ($_POST['type'] == "password") {
        if (isset($_POST['password'])) { echo "<p> Wachtwoord: ".password_hash($_POST['password'], PASSWORD_DEFAULT)." </p>"; }
    }
}
?>

<form method='post'>
    PermissionFlags: <input type='text' name='permissionFlags'><br>
    <input type="hidden" name="type" value="reversePermission" />
    <input type='submit'>
</form>

<form method='post'>
    <?php
    foreach ($permissionsList as $name => $value) {
        echo "".$name.": <input type='checkbox' name='".$name."' value='0'><br>";
    }
    ?>

    <input type="hidden" name="type" value="permission" />
    <input type='submit'>
</form>

<form method='post'>
  Wachtwoord: <input type='text' name='password'><br>
  <input type="hidden" name="type" value="password" />
  <input type='submit'>
</form>
