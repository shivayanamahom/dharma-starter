<?php

/**
 * Макет шапки "default": верхняя плашка (меню + адрес) и главная шапка
 * (бургер, логотип, поиск, контакты, соцсети, корзина, кнопка обратного звонка),
 * затем оффканвас-меню.
 *
 * Новые макеты кладутся в эту же папку (header/<имя>.php) и сразу появляются
 * в параметрах шаблона в списке «Макет шапки». Общие части (оффканвас)
 * лежат в header/partials/.
 *
 * Переменные приходят из index.php: $this (HtmlDocument), $params, $e (экранирование),
 * $logoUrl, $logoAlt, $contactPhone, $phoneHref, $contactEmail, $contactAddress, $socialLinks.
 *
 * @package     Joomla.Site
 * @subpackage  Templates.dharma_starter
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

?>
	<div class="dharma-header">
		<?php if ($this->countModules('menu', true) || $contactAddress !== '') : ?>
			<div class="dharma-header__topbar d-none d-lg-block">
				<div class="container dharma-header__topbar-inner">
					<?php if ($this->countModules('menu', true)) : ?>
						<nav class="dharma-header__topbar-nav" aria-label="<?php echo Text::_('TPL_DHARMA_STARTER_PRIMARY_NAV'); ?>">
							<jdoc:include type="modules" name="menu" style="none" />
						</nav>
					<?php endif; ?>

					<?php if ($contactAddress !== '') : ?>
						<div class="dharma-header__topbar-address">
							<span class="ico-pin" aria-hidden="true"></span>
							<span><?php echo $e($contactAddress); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<header class="dharma-header__main">
		<div class="container dharma-header__main-inner">
				<?php if ($this->countModules('menu', true)) : ?>
					<button type="button" class="dharma-header__burger" data-bs-toggle="offcanvas" data-bs-target="#dharmaOffcanvas" aria-controls="dharmaOffcanvas" aria-label="<?php echo Text::_('TPL_DHARMA_STARTER_MENU'); ?>">
						<span class="dharma-header__burger-icon" aria-hidden="true"><span></span><span></span><span></span></span>
						<span class="dharma-header__burger-text d-none d-lg-inline"><?php echo Text::_('TPL_DHARMA_STARTER_MENU'); ?></span>
					</button>
				<?php endif; ?>

				<div class="dharma-header__brand">
					<a class="dharma-brand" href="<?php echo $e(Uri::root()); ?>" aria-label="<?php echo $logoAlt; ?>">
						<img src="<?php echo $e($logoUrl); ?>" alt="<?php echo $logoAlt; ?>" width="238" height="48" fetchpriority="high" decoding="async">
					</a>
				</div>

				<?php if ($this->countModules('search', true)) : ?>
					<div class="dharma-header__search d-none d-lg-block">
						<jdoc:include type="modules" name="search" style="none" />
					</div>
				<?php endif; ?>

				<?php if ($contactPhone !== '' || $contactEmail !== '') : ?>
					<div class="dharma-header__contacts d-none d-lg-flex">
						<?php if ($contactPhone !== '') : ?>
							<a class="dharma-header__phone" href="<?php echo $e($phoneHref); ?>"><?php echo $e($contactPhone); ?></a>
						<?php endif; ?>
						<?php if ($contactEmail !== '') : ?>
							<a class="dharma-header__mail" href="mailto:<?php echo $e($contactEmail); ?>"><?php echo $e($contactEmail); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ($socialLinks) : ?>
					<div class="dharma-header__social d-none d-xxl-flex">
						<?php foreach ($socialLinks as $key => $social) : ?>
							<a href="<?php echo $e($social['url']); ?>" target="_blank" rel="noopener" title="<?php echo $e($social['label']); ?>" aria-label="<?php echo $e($social['label']); ?>">
								<span class="ico-<?php echo $e($key); ?>" aria-hidden="true"></span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ($this->countModules('cart', true)) : ?>
					<div class="dharma-header__cart">
						<jdoc:include type="modules" name="cart" style="none" />
					</div>
				<?php endif; ?>

				<?php if ($this->countModules('header-actions', true)) : ?>
					<div class="dharma-header__actions"><jdoc:include type="modules" name="header-actions" style="none" /></div>
				<?php endif; ?>

				<?php if ($contactPhone !== '') : ?>
					<a class="dharma-header__phone-call d-inline-flex d-lg-none" href="<?php echo $e($phoneHref); ?>" aria-label="<?php echo Text::sprintf('TPL_DHARMA_STARTER_CALL_US', $contactPhone); ?>">
						<span class="dharma-header__phone-call-ring" aria-hidden="true"></span>
						<span class="ico-phone" aria-hidden="true"></span>
					</a>
				<?php endif; ?>

				<a class="knopka dharma-header__callback callback ripple-hover d-none d-xxl-inline-flex" href="#">
					<?php echo Text::_('TPL_DHARMA_STARTER_CALLBACK'); ?>
					<span class="ar-arrow" aria-hidden="true"></span>
				</a>
			</div>
	</header>

<?php include __DIR__ . '/partials/offcanvas.php'; ?>
