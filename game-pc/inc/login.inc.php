<div class="content">
    <form action="PHP/login.php" method="post">
        <div class="login">
            E-mail
            <input type="text" id="1" placeholder="gebruikersnaam@bookonshelf.nl" name="email" required>

            Wachtwoord
            <input type="password" placeholder="Wachtwoord" name="password" required>

            <p>Don't have a account?<a href="index.php?page=register" class="register-button"> Register here</a></p>


            <?php
            if(isset($_SESSION['notification'])){
                echo $_SESSION['notification'];
                unset($_SESSION['notification']);
            }
            ?>

           <br> <button type="submit">Login</button> <br>
        </div>
    </form> <!--Login-->
</div>


