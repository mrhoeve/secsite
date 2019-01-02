<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login Form</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body>
<div class="form">
    <h1>Login Form</h1>
    <form action="authenticate.php" method="post">
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="password" id="password" autocomplete="none" placeholder="Password">
        <progress max="100" value="0" id="strength" style="width: 300px"></progress>
        <input type="submit">
    </form>
</div>
</body>

<script type="text/javascript">
    var pass = document.getElementById('password');
    pass.addEventListener('keyup', function() {
        var strengthBar = document.getElementById('strength');
        var strength = 0;
        var passValue = pass.value;
        if(passValue.match(/[a-zA-Z0-9][a-zA-Z0-9]+/)) {
            strength +=1;
        }
        if(passValue.match(/[~<>?]+/)) {
            strength +=1;
        }
        if(passValue.match(/[!@#$%^&*()]+/)) {
            strength +=1;
        }
        if(passValue.length > 5) {
            strength +=1;
        }
        strengthBar.value = (strength === 0 ? 0 : (strength + 1) * 20 );
    })
</script>
</html>
