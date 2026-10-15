// Стрелка у пунктов с выпадающим подменю в верхней плашке — Joomla сама
// добавляет класс "parent" родительскому <li>, а вот маленькую стрелку
// внутри ссылки нужно дорисовать через JS (как у parmaled).
document.querySelectorAll('.dharma-header__topbar-nav .mod-menu > li.parent > a').forEach((link) => {
  if (link.querySelector(':scope > .menu-chevron')) {
    return;
  }

  const chevron = document.createElement('span');
  chevron.className = 'menu-chevron';
  chevron.setAttribute('aria-hidden', 'true');
  link.append(chevron);
});

// Кнопка-иконка корзины в шапке стартует disabled в разметке RadicalMart и
// сама по себе включается только после клика по ней же (замкнутый круг) —
// синхронизируем её с реальным состоянием корзины по родным событиям
// компонента, как на parmaled.
const cartDisplayButtons = document.querySelectorAll(
  '[radicalmart-cart="display_module"], [data-radicalmart-cart="display_module"]'
);

const syncCartDisplayButtons = (cart) => {
  const hasProducts = Boolean(cart && cart.total && Number(cart.total.quantity) > 0);

  cartDisplayButtons.forEach((button) => {
    button.disabled = !hasProducts;
    button.setAttribute('aria-disabled', String(!hasProducts));
  });
};

document.addEventListener('onRadicalMartCartAfterUpdateDisplayData', (event) => {
  syncCartDisplayButtons(event.detail);
});

document.addEventListener('onRadicalMartCartAfterRemoveProduct', (event) => {
  syncCartDisplayButtons(event.detail?.cart);
});

document.querySelectorAll('.ripple-hover').forEach((button) => {
  button.addEventListener('mouseenter', function (event) {
    const ripple = document.createElement('span');
    const rect = this.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);

    ripple.className = 'ripple';
    ripple.style.width = ripple.style.height = `${size}px`;
    ripple.style.left = `${event.clientX - rect.left - size / 2}px`;
    ripple.style.top = `${event.clientY - rect.top - size / 2}px`;

    this.appendChild(ripple);
    window.setTimeout(() => ripple.remove(), 800);
  });
});
