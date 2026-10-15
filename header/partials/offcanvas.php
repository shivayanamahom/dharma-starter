<?php

/**
 * Оффканвас-меню: клон модуля mod_menu из позиции "menu" с макетом offcanvas
 * (html/mod_menu/offcanvas.php), контакты и соцсети. Подключается из макетов шапки.
 *
 * Переменные приходят из index.php через макет шапки: $e, $logoUrl, $logoAlt,
 * $contactPhone, $phoneHref, $contactEmail, $contactAddress, $socialLinks, $this.
 *
 * @package     Joomla.Site
 * @subpackage  Templates.dharma_starter
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

?>
	<div class="offcanvas offcanvas-end dharma-offcanvas" tabindex="-1" id="dharmaOffcanvas" aria-labelledby="dharmaOffcanvasTitle">
		<div class="offcanvas-header">
			<a class="dharma-brand" href="<?php echo $e(Uri::root()); ?>" id="dharmaOffcanvasTitle" aria-label="<?php echo $logoAlt; ?>">
				<img src="<?php echo $e($logoUrl); ?>" alt="<?php echo $logoAlt; ?>" width="119" height="24" decoding="async">
			</a>
			<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="<?php echo Text::_('JCLOSE'); ?>"></button>
		</div>
		<div class="offcanvas-body">
			<?php if ($this->countModules('menu', true)) : ?>
				<?php foreach (ModuleHelper::getModules('menu') as $menuModule) :
					if ($menuModule->module !== 'mod_menu') {
						continue;
					}

					// Тот же модуль меню, что в верхней плашке, просто со своим
					// layout'ом (html/mod_menu/offcanvas.php) — раскрывающиеся
					// вложенные пункты через bootstrap.collapse. Отдельного
					// модуля заводить не нужно.
					$mobileMenuModule = clone $menuModule;
					$mobileMenuModule->contentRendered = false;
					unset($mobileMenuModule->content);
					$mobileMenuParams = new Registry($mobileMenuModule->params);
					$mobileMenuParams->set('layout', 'offcanvas');
					$mobileMenuModule->params = (string) $mobileMenuParams;
					echo ModuleHelper::renderModule($mobileMenuModule, ['style' => 'none']);
				endforeach; ?>
			<?php endif; ?>

			<?php if ($contactPhone !== '' || $contactEmail !== '' || $contactAddress !== '' || $socialLinks) : ?>
				<div class="dharma-offcanvas__contacts">
					<?php if ($contactPhone !== '') : ?>
						<div class="dharma-offcanvas__contacts-row">
							<span class="dharma-offcanvas__contacts-icon-wrap"><span class="dharma-offcanvas__contacts-icon ico-phone" aria-hidden="true"></span></span>
							<div class="dharma-offcanvas__contacts-text">
								<a href="<?php echo $e($phoneHref); ?>"><?php echo $e($contactPhone); ?></a>
							</div>
						</div>
					<?php endif; ?>
					<?php if ($contactEmail !== '') : ?>
						<div class="dharma-offcanvas__contacts-row">
							<span class="dharma-offcanvas__contacts-icon-wrap"><span class="dharma-offcanvas__contacts-icon ico-mail" aria-hidden="true"></span></span>
							<a class="dharma-offcanvas__contacts-text" href="mailto:<?php echo $e($contactEmail); ?>"><?php echo $e($contactEmail); ?></a>
						</div>
					<?php endif; ?>
					<?php if ($contactAddress !== '') : ?>
						<div class="dharma-offcanvas__contacts-row">
							<span class="dharma-offcanvas__contacts-icon-wrap"><span class="dharma-offcanvas__contacts-icon ico-pin" aria-hidden="true"></span></span>
							<span class="dharma-offcanvas__contacts-text"><?php echo $e($contactAddress); ?></span>
						</div>
					<?php endif; ?>
					<?php if ($socialLinks) : ?>
						<div class="dharma-header__social dharma-offcanvas__social">
							<?php foreach ($socialLinks as $key => $social) : ?>
								<a href="<?php echo $e($social['url']); ?>" target="_blank" rel="noopener" title="<?php echo $e($social['label']); ?>" aria-label="<?php echo $e($social['label']); ?>">
									<span class="ico-<?php echo $e($key); ?>" aria-hidden="true"></span>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
