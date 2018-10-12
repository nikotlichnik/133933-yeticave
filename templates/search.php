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

<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span><?= htmlspecialchars($user_search_query) ?></span>»</h2>
        <?php if ($lots): ?>
            <ul class="lots__list">
                <?= include_template('_grid.php', ['lots' => $lots]) ?>
            </ul>
        <?php else: ?>
            <p>Ничего не найдено по вашему запросу</p>
        <?php endif; ?>
    </section>
    <?php if ($lots): ?>
        <ul class="pagination-list">
            <li class="pagination-item pagination-item-prev">
                <a <?= get_href_search_attr($user_search_query, $previous_page) ?>>Назад</a>
            </li>

            <?php foreach ($page_range as $page): ?>
                <?php if ($page === $cur_page): ?>
                    <li class="pagination-item pagination-item-active">
                        <a><?= $page ?></a>
                    </li>
                <?php else: ?>
                    <li class="pagination-item">
                        <a <?= get_href_search_attr($user_search_query, $page) ?>><?= $page ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>

            <li class="pagination-item pagination-item-next">
                <a <?= get_href_search_attr($user_search_query, $next_page) ?>>Вперед</a>
            </li>
        </ul>
    <?php endif; ?>
</div>
