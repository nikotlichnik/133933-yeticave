<?= include_template('_navigation.php', ['categories' => $categories]) ?>

<?php $form_class = isset($errors) ? 'form--invalid' : ''; ?>
<form class="form container  <?= $form_class; ?>" action="sign-up.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Регистрация нового аккаунта</h2>
    <?php
    $field_name = 'email';
    $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
    $error_message = $errors[$field_name] ?? '';
    $input_value = $user[$field_name] ?? '';
    ?>
    <div class="form__item  <?= $input_class; ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= htmlspecialchars($input_value); ?>">
        <span class="form__error"><?= $error_message; ?></span>
    </div>
    <?php
    $field_name = 'password';
    $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
    $error_message = $errors[$field_name] ?? '';
    $input_value = $user[$field_name] ?? '';
    ?>
    <div class="form__item  <?= $input_class; ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= htmlspecialchars($input_value); ?>">
        <span class="form__error"><?= $error_message; ?></span>
    </div>
    <?php
    $field_name = 'name';
    $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
    $error_message = $errors[$field_name] ?? '';
    $input_value = $user[$field_name] ?? '';
    ?>
    <div class="form__item  <?= $input_class; ?>">
        <label for="name">Имя*</label>
        <input id="name" type="text" name="name" placeholder="Введите имя" value="<?= htmlspecialchars($input_value); ?>">
        <span class="form__error"><?= $error_message; ?></span>
    </div>
    <?php
    $field_name = 'message';
    $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
    $error_message = $errors[$field_name] ?? '';
    $input_value = $user[$field_name] ?? '';
    ?>
    <div class="form__item  <?= $input_class; ?>">
        <label for="message">Контактные данные*</label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?= htmlspecialchars($input_value); ?></textarea>
        <span class="form__error"><?= $error_message; ?></span>
    </div>
    <?php
    $field_name = 'avatar';
    $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
    $error_message = $errors[$field_name] ?? '';
    ?>
    <div class="form__item form__item--file form__item--last  <?= $input_class; ?>">
        <label>Аватар</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="img/avatar.jpg" width="113" height="113" alt="Ваш аватар">
            </div>
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="avatar" id="photo2" value="">
            <label for="photo2">
                <span>+ Добавить</span>
            </label>
        </div>
        <span class="form__error"><?= $error_message; ?></span>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="login.php">Уже есть аккаунт</a>
</form>
