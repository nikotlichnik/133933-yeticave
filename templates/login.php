<?= include_template('_navigation.php', ['categories' => $categories]) ?>

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
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=htmlspecialchars($input_value); ?>">
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
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?=htmlspecialchars($input_value); ?>">
        <span class="form__error"><?= $error_message; ?></span>
    </div>
    <button type="submit" class="button">Войти</button>
</form>
