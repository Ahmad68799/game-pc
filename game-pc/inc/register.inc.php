

<body>
<!-- register -->

<div class="sign-up">
    <form action="PHP/register.php"method="post">
<h1>sign up</h1>

<div class="login">
    <label>First Name / Prefix / Last Name</label>
    <div class="name-group">
        <input type="text" placeholder="Jan" name="username" required>
        <input type="text" placeholder="de" name="prefix">
        <input type="text" placeholder="Jansen" name="lastname">
    </div>
    <div class="register">
        <h1>Address</h1>
    <label>Place</label>
    <input name="place" type="text" required>

    <label>Street</label>
    <input name="street" type="text" required>

    <label>House Number</label>
    <input name="housenumber" type="text" required>

    <label>Zip Code</label>
    <input name="zipcode" type="text" required>
    </div>
    <label>Email</label>
    <input name="email" type="email" required>

    <label>Password</label>
    <input name="password" type="password" required>

    <label>Confirm Password</label>
    <input name="confirm_password" type="text" required>


    <button type="submit">Login</button>


    <!-- minimaal 8 karakters 1 hoofdletter 1 kleine letter 1 cijfer 1 speciaal karakter-->
</div>
    </form>
</div>

</body>