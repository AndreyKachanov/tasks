<div class="login">
    <form method="POST" id="form_login" class="container" novalidate>
        
        <div id="error-auth" class="form-group invalid-feedback text-center <?=$errors['auth']?>">Не верный логин или пароль.</div>
        
        <div class="form-group row justify-content-center">
            <div class="col-xs-12 col-sm-10 col-md-5 col-lg-4">
                <input type="text" name="login" class="form-control <?=$errors['login']?>" value="<?=$login?>" placeholder="Логин" required>
                <div class="invalid-feedback">Введите логин.</div>
            </div>
        </div>

        <div class="form-group row justify-content-center">
            <div class="col-xs-12 col-sm-10 col-md-5 col-lg-4">
                <input type="password" name="password" class="form-control <?=$errors['password']?>" value="<?=$password?>" placeholder="Пароль" required>
                <div class="invalid-feedback">Введите пароль.</div>
            </div>
        </div>
        
        <div class="form-group row justify-content-center">
            <div class="col-xs-12 col-sm-10 col-md-5 col-lg-4 d-flex justify-content-center">
                <label class="form-check-label"><input type="checkbox" name="remember" class="form-check-input"> Запомнить</label>
                <button type="submit" class="btn btn-secondary">Войти</button>
            </div> 
        </div>
    </form>
</div>