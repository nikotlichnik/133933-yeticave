<nav class="nav">
    <ul class="nav__list container">
        <li class="nav__item">
            <a href="all-lots.html">Доски и лыжи</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Крепления</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Ботинки</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Одежда</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Инструменты</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Разное</a>
        </li>
    </ul>
</nav>
<?php $form_class = isset($errors) ? 'form--invalid' : ''; ?>
<form class="form container" action="login.php" method="post"> <!-- form--invalid -->
    <h2>Вход</h2>
    <?php
    $field_name = 'email';
    $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
    $error_message = $errors[$field_name] ?? '';
    $input_value = $login[$field_name] ?? '';
    ?>
    <div class="form__item <?= $input_class; ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= $input_value; ?>">
        <span class="form__error"><?= $error_message; ?></span>
    </div>
    <?php
    $field_name = 'password';
    $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
    $error_message = $errors[$field_name] ?? '';
    $input_value = $login[$field_name] ?? '';
    ?>
    <div class="form__item form__item--last <?= $input_class; ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= $input_value; ?>">
        <span class="form__error"><?= $error_message; ?></span>
    </div>
    <button type="submit" class="button">Войти</button>
</form>
