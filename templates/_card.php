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
            <div class="lot__timer timer"><?=get_timer($item['expiration_date']); ?></div>
        </div>
    </div>
</li>
