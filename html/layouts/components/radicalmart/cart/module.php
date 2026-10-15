<?php
/*
 * @package     RadicalMart Package
 * @subpackage  com_radicalmart
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\Component\RadicalMart\Site\Helper\MediaHelper;

extract($displayData);

if (empty($cart)) {
    return;
}

$totalIsRequest = isset($cart->total['final']) && (float) $cart->total['final'] === 0.0;
$totalLabel     = $totalIsRequest ? 'Цена по запросу' : $cart->total['final_string'];
?>
<?php if ($cart): ?>
<div class="offcanvas offcanvas-end cart-offcanvas" tabindex="-1" id="radicalmartCartModule">
	<div class="cart-offcanvas__inner">
		<header class="cart-offcanvas__header">
			<div class="cart-offcanvas__title">
				<span class="ico-cart" aria-hidden="true"></span>
				<?php echo Text::_('COM_RADICALMART_CART'); ?>
			</div>
			<button type="button" class="icon-close-bars cart-offcanvas__close" data-bs-dismiss="offcanvas" aria-label="<?php echo Text::_('JCLOSE'); ?>"></button>
		</header>

		<?php if (!empty($cart->products)): ?>
			<div class="cart-offcanvas__body">
				<?php foreach ($cart->products as $p => $product): ?>
					<article radicalmart-cart="product" class="cart-item"
						data-id="<?php echo $product->id; ?>" data-key="<?php echo $p; ?>" data-cart-product="1">
						<a href="<?php echo $product->link; ?>" class="cart-item__image">
							<?php echo MediaHelper::renderImage(
								'com_radicalmart.products.cart.module',
								$product->image,
								[
									'alt'     => $product->title,
									'loading' => 'lazy',
									'class'   => 'cart-item__image-el',
								],
								[
									'product_id' => $product->id,
									'no_image'   => true,
									'thumb'      => true,
								]
							); ?>
						</a>

						<div class="cart-item__content">
							<?php if ($product->category): ?>
								<a href="<?php echo $product->category->link; ?>" class="cart-item__category">
									<?php echo htmlspecialchars($product->category->title, ENT_QUOTES, 'UTF-8'); ?>
								</a>
							<?php endif; ?>
							<a href="<?php echo $product->link; ?>" class="cart-item__title">
								<?php echo htmlspecialchars($product->title, ENT_QUOTES, 'UTF-8'); ?>
							</a>

							<?php if (!empty($product->extra_display)): ?>
								<div class="cart-item__extras">
									<?php foreach ($product->extra_display as $extra): ?>
										<?php if (!empty($extra['html'])): ?>
											<span class="cart-item__extra"><?php echo $extra['html']; ?></span>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<div class="cart-item__bottom">
								<div class="cart-quantity" aria-label="<?php echo Text::_('COM_RADICALMART_QUANTITY'); ?>">
									<button type="button" radicalmart-cart="quantity_minus" class="cart-quantity__button" aria-label="Уменьшить количество">−</button>
									<input radicalmart-cart="quantity" type="number" name="quantity" data-set="1" class="cart-quantity__input"
										step="<?php echo $product->quantity['step']; ?>" min="<?php echo $product->quantity['min']; ?>"
										<?php if (!empty($product->quantity['max'])) echo 'max="' . $product->quantity['max'] . '"'; ?>
										value="<?php echo $product->order['quantity']; ?>">
									<button type="button" radicalmart-cart="quantity_plus" class="cart-quantity__button" aria-label="Увеличить количество">+</button>
								</div>
								<div class="cart-item__price">
									<?php if (stripos((string) $product->order['final_string'], 'запрос') === false): ?>
										<span class="cart-item__unit-price"><?php echo $product->order['final_string']; ?><?php if (!empty($product->quantity['units_string_short'])): ?> / <?php echo $product->quantity['units_string_short']; ?><?php endif; ?></span>
									<?php endif; ?>
									<strong radicalmart-cart-display="products.<?php echo $p; ?>.order.sum_final_string">
										<?php echo $product->order['sum_final_string']; ?>
									</strong>
								</div>
							</div>
						</div>

						<button type="button" class="icon-close-bars cart-item__remove" radicalmart-cart="remove" aria-label="Удалить товар"></button>
					</article>
				<?php endforeach; ?>
			</div>

			<footer class="cart-offcanvas__footer">
				<div class="cart-offcanvas__total">
					<span><?php echo Text::_('COM_RADICALMART_TOTAL'); ?></span>
					<strong radicalmart-cart-display="total.final_string" data-cart-request-total="Цена по запросу"><?php echo $totalLabel; ?></strong>
				</div>
				<div class="cart-offcanvas__actions">
					<a href="<?php echo $cart->link; ?>" class="knopka knopka2 ripple-hover cart-offcanvas__action">
						<?php echo Text::_('COM_RADICALMART_CART'); ?>
					</a>
					<a href="<?php echo $cart->checkout; ?>" class="knopka ripple-hover cart-offcanvas__action">
						<span>Оформить</span>
						<span class="ar-arrow" aria-hidden="true"></span>
					</a>
				</div>
			</footer>
		<?php else: ?>
			<div class="cart-offcanvas__empty">
				<span class="ico-cart" aria-hidden="true"></span>
				<p><?php echo Text::_('COM_RADICALMART_CART_EMPTY'); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>
