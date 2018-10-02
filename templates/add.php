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
<form class="form form--add-lot container <?= $form_class; ?>" action="add.php" method="post"
      enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <?php
        $field_name = 'lot-name';
        $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
        $error_message = $errors[$field_name] ?? '';
        $input_value = $lot[$field_name] ?? '';
        ?>
        <div class="form__item <?= $input_class; ?>"> <!-- form__item--invalid -->
            <label for="lot-name">Наименование</label>
            <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота"
                   value="<?= $input_value; ?>">
            <span class="form__error"><?= $error_message; ?></span>
        </div>

        <?php
        $field_name = 'category';
        $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
        $error_message = $errors[$field_name] ?? '';
        $input_value = $lot[$field_name] ?? '';
        ?>
        <div class="form__item <?= $input_class; ?>">
            <label for="category">Категория</label>
            <select id="category" name="category">
                <option disabled selected>Выберите категорию</option>
                <?php foreach ($categories as $category): ?>
                    <?php if($category['id'] === $input_value): ?>
                        <option value="<?= $category['id']; ?>" selected><?= $category['name']; ?></option>
                    <?php else: ?>
                        <option value="<?= $category['id']; ?>"><?= $category['name']; ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <span class="form__error"><?= $error_message; ?></span>
        </div>
    </div>

    <?php
    $field_name = 'message';
    $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
    $error_message = $errors[$field_name] ?? '';
    $input_value = $lot[$field_name] ?? '';
    ?>
    <div class="form__item form__item--wide <?= $input_class; ?>">
        <label for="message">Описание</label>
        <textarea id="message" name="message" placeholder="Напишите описание лота"><?= $input_value; ?></textarea>
        <span class="form__error"><?= $error_message; ?></span>
    </div>
    <?php
    $field_name = 'lot-photo';
    $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
    $error_message = $errors[$field_name] ?? '';
    ?>
    <div class="form__item form__item--file <?= $input_class; ?>"> <!-- form__item--uploaded -->
        <label>Изображение</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="img/avatar.jpg" width="113" height="113" alt="Изображение лота">
            </div>
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" name="lot-photo" type="file" id="photo2">
            <label for="photo2">
                <span>+ Добавить</span>
            </label>
        </div>
        <span class="form__error"><?= $error_message; ?></span>
    </div>
    <div class="form__container-three">
        <?php
        $field_name = 'lot-rate';
        $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
        $error_message = $errors[$field_name] ?? '';
        $input_value = $lot[$field_name] ?? '';
        ?>
        <div class="form__item form__item--small <?= $input_class; ?>">
            <label for="lot-rate">Начальная цена</label>
            <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?= $input_value; ?>">
            <span class="form__error"><?= $error_message; ?></span>
        </div>

        <?php
        $field_name = 'lot-step';
        $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
        $error_message = $errors[$field_name] ?? '';
        $input_value = $lot[$field_name] ?? '';
        ?>
        <div class="form__item form__item--small <?= $input_class; ?>">
            <label for="lot-step">Шаг ставки</label>
            <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?= $input_value; ?>">
            <span class="form__error"><?= $error_message; ?></span>
        </div>

        <?php
        $field_name = 'lot-date';
        $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
        $error_message = $errors[$field_name] ?? '';
        $input_value = $lot[$field_name] ?? '';
        ?>
        <div class="form__item <?= $input_class; ?>">
            <label for="lot-date">Дата окончания торгов</label>
            <input class="form__input-date" id="lot-date" type="text" name="lot-date" placeholder="ДД.ММ.ГГГГ" value="<?= $input_value; ?>">
            <span class="form__error"><?= $error_message; ?></span>
        </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Добавить лот</button>
</form>
