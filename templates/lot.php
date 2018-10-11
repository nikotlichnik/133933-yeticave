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
<section class="lot-item container">
    <h2><?= $lot['name']; ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?= $lot['img_path']; ?>" width="730" height="548" alt="<?= $lot['name']; ?>">
            </div>
            <p class="lot-item__category">Категория: <span><?= $lot['category']; ?></span></p>
            <p class="lot-item__description"><?= $lot['description']; ?></p>
        </div>
        <div class="lot-item__right">
            <?php if ($user): ?>
                <div class="lot-item__state">
                    <div class="lot-item__timer timer"><?= get_timer($lot['expiration_date']); ?></div>
                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <span class="lot-item__amount">Текущая цена</span>
                            <span class="lot-item__cost"><?= format_price($lot['current_price']); ?></span>
                        </div>
                        <div class="lot-item__min-cost">
                            Мин. ставка <span><?= format_price($lot['min_bet']); ?></span>
                        </div>
                    </div>
                    <?php if ($is_allowed_to_bet): ?>
                        <form class="lot-item__form" action="lot.php?id=<?= $lot['id']; ?>" method="post">
                            <?php
                            $field_name = 'cost';
                            $input_class = isset($errors[$field_name]) ? 'form__item--invalid' : '';
                            $error_message = $errors[$field_name] ?? '';
                            $input_value = isset($errors[$field_name]) ? $bet[$field_name] : '';
                            ?>
                            <p class="lot-item__form-item <?= $input_class; ?>">
                                <label for="cost">Ваша ставка</label>
                                <input id="cost" type="number" name="cost" placeholder="<?= $lot['min_bet']; ?>"
                                       value="<?= $input_value; ?>">
                                <span class="form__error"><?= $error_message; ?></span>
                            </p>
                            <button type="submit" class="button">Сделать ставку</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="history">
                <h3>История ставок (<span><?=count($bets); ?></span>)</h3>
                <table class="history__list">
                    <?php foreach ($bets as $key => $item): ?>
                        <tr class="history__item">
                            <td class="history__name"><?= $item['name']; ?></td>
                            <td class="history__price"><?= format_price($item['bet']); ?></td>
                            <td class="history__time"><?= format_bet_date($item['date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>

</section>
