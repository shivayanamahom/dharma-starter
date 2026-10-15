<?php
/*
 * @package     RadicalMart Package
 * @subpackage  com_radicalmart
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\Component\RadicalMart\Site\Helper\MediaHelper;

extract($displayData);

$order    = !empty($product->order) ? (array) $product->order : [];
$price    = !empty($product->price) ? (array) $product->price : [];
$isRequest = !empty($order['_price_request']) || !empty($price['_price_request']);
$priceString = $isRequest
	? (string) ($order['_price_request_label'] ?? $price['_price_request_label'] ?? 'Цена по запросу')
	: (string) ($order['sum_final_string'] ?? $price['final_string'] ?? '');
$quantityString = (string) ($order['quantity_string_short'] ?? $order['quantity_string'] ?? '');
?>
<div class="cart-toast" id="radicalmartCartNotificationAdd" role="status" aria-live="polite">
	<div class="cart-toast__head">
		<span class="ico-cart" aria-hidden="true"></span>
		<span><?php echo Text::_('COM_RADICALMART_CART_NOTIFICATION_ADD'); ?></span>
		<button type="button" class="icon-close-bars cart-toast__close" id="radicalmartCartNotificationAddClose" aria-label="<?php echo Text::_('JCLOSE'); ?>"></button>
	</div>
	<div class="cart-toast__body">
		<a href="<?php echo $product->link; ?>" class="cart-toast__image">
			<?php echo MediaHelper::renderImage(
				'com_radicalmart.products.cart.module', $product->image,
				['alt' => $product->title, 'loading' => 'eager', 'class' => 'cart-toast__image-el'],
				['product_id' => $product->id, 'no_image' => true, 'thumb' => true]
			); ?>
		</a>
		<div class="cart-toast__content">
			<a href="<?php echo $product->link; ?>" class="cart-toast__title"><?php echo htmlspecialchars($product->title, ENT_QUOTES, 'UTF-8'); ?></a>
			<?php if ($quantityString || $priceString): ?>
				<div class="cart-toast__details">
					<?php if ($quantityString): ?><span>Количество: <?php echo htmlspecialchars($quantityString, ENT_QUOTES, 'UTF-8'); ?></span><?php endif; ?>
					<?php if ($priceString): ?><strong class="cart-toast__price"><?php echo htmlspecialchars($priceString, ENT_QUOTES, 'UTF-8'); ?></strong><?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="cart-toast__progress" id="radicalmartCartNotificationAddProgress"></div>
</div>
<script>
(() => {
	const toast = document.getElementById('radicalmartCartNotificationAdd');
	const progress = document.getElementById('radicalmartCartNotificationAddProgress');
	const close = document.getElementById('radicalmartCartNotificationAddClose');
	let timer;
	const dismiss = () => {
		clearTimeout(timer);
		toast.classList.add('is-leaving');
		toast.addEventListener('animationend', () => toast.remove(), { once: true });
	};
	if (progress) progress.style.animationDuration = '4500ms';
	if (close) close.addEventListener('click', dismiss);
	timer = window.setTimeout(dismiss, 4500);
	toast.addEventListener('mouseenter', () => clearTimeout(timer));
	toast.addEventListener('mouseleave', () => { timer = window.setTimeout(dismiss, 1500); });
})();
</script>
