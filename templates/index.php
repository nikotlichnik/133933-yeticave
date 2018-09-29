<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $key => $value): ?>
            <li class="promo__item promo__item--boards">
                <a class="promo__link" href="pages/all-lots.html"><?=$value; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?php foreach($lots as $key => $item): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=$item['img_path']; ?>" width="350" height="260" alt="<?=htmlspecialchars($item['name']); ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=htmlspecialchars($item['category']); ?></span>
                    <h3 class="lot__title">
                        <a class="text-link" href="lot.php?id=<?=$item['id']?>"><?=htmlspecialchars($item['name']); ?></a>
                    </h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=format_price($item['start_price']); ?></span>
                        </div>
                        <div class="lot__timer timer"><?=get_timer(); ?></div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
