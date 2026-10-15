<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.dharma_starter
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

/** @var Joomla\CMS\Document\HtmlDocument $this */

$app    = Factory::getApplication();
$input  = $app->getInput();
$params = $this->params;
$wa     = $this->getWebAssetManager();

// Короткий помощник экранирования для шаблона и подключаемых макетов шапки.
$e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$directionPreset = 'template.cassiopeia.' . ($this->direction === 'rtl' ? 'rtl' : 'ltr');

// Font Awesome нужен иконкам ядра Joomla (логин, пагинация и т. п.); шаблон рисует свои иконки сам.
// Пресет Cassiopeia тянет его как зависимость, а убрать зависимость нельзя, поэтому
// при выключенной опции перерегистрируем тот же файл CSS без зависимостей.
$cassiopeiaStyle = $this->direction === 'rtl' ? 'template.cassiopeia.rtl' : 'template.cassiopeia.ltr';

if ((int) $params->get('load_fontawesome', 1) !== 1) {
	$wa->registerStyle(
		$cassiopeiaStyle,
		'media/templates/site/cassiopeia/css/' . ($this->direction === 'rtl' ? 'template-rtl.min.css' : 'template.min.css')
	);
}


$wa->usePreset($directionPreset)
	->useStyle('template.dharma.fonts')
	->useStyle('template.dharma.user')
	->useScript('bootstrap.collapse')
	->useScript('template.dharma.userjs');

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');

// Поле "media" хранит значение вида images/x.png#joomlaImage://local-images/x.png?width=..;
// оставляем только путь. Старые значения (просто путь) тоже подходят.
$cleanMedia = static fn (string $value): string => ltrim((string) strtok((string) HTMLHelper::cleanImageURL($value)->url, '#'), '/');

$faviconTypes = ['svg' => 'image/svg+xml', 'png' => 'image/png', 'ico' => 'image/x-icon', 'gif' => 'image/gif', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'webp' => 'image/webp', 'avif' => 'image/avif'];

$faviconPath = $cleanMedia((string) $params->get('favicon_path', 'images/dharma_starter/favicon.svg')) ?: 'images/dharma_starter/favicon.svg';
$faviconType = $faviconTypes[strtolower((string) pathinfo($faviconPath, PATHINFO_EXTENSION))] ?? 'image/svg+xml';
$this->addHeadLink(Uri::root(true) . '/' . $faviconPath, 'icon', 'rel', ['type' => $faviconType]);

$logoPath     = $cleanMedia((string) $params->get('logo_path', 'images/dharma_starter/logo.png')) ?: 'images/dharma_starter/logo.png';
$logoUrl      = Uri::root(true) . '/' . $logoPath;
$brandName    = (string) $params->get('brand_name', 'Dharma');
$logoAlt      = $e($brandName);
$contactPhone = trim((string) $params->get('contact_phone', '+7 (000) 000-00-00'));
$phoneHref    = 'tel:' . preg_replace('/[^0-9+]/', '', $contactPhone);
$contactEmail   = trim((string) $params->get('contact_email', ''));
$contactAddress = trim((string) $params->get('contact_address', ''));

$socialLinks = [];

if ((int) $params->get('social_telegram_enabled', 0) === 1) {
	$url = trim((string) $params->get('social_telegram_url', ''));

	if ($url !== '') {
		$socialLinks['telegram'] = ['url' => $url, 'label' => 'Telegram'];
	}
}

if ((int) $params->get('social_max_enabled', 0) === 1) {
	$url = trim((string) $params->get('social_max_url', ''));

	if ($url !== '') {
		$socialLinks['max'] = ['url' => $url, 'label' => 'MAX'];
	}
}

if ((int) $params->get('social_vk_enabled', 0) === 1) {
	$url = trim((string) $params->get('social_vk_url', ''));

	if ($url !== '') {
		$socialLinks['vk'] = ['url' => $url, 'label' => 'VKontakte'];
	}
}

$option   = $input->getCmd('option', '');
$view     = $input->getCmd('view', '');
$layout   = $input->getCmd('layout', '');
$task     = $input->getCmd('task', '');
$itemid   = $input->getCmd('Itemid', '');
$active   = $app->getMenu()->getActive();
$pageclass = $active ? trim((string) $active->getParams()->get('pageclass_sfx', '')) : '';
$sticky   = (int) $params->get('sticky_header', 0) === 1;

// Макет шапки: файл из папки header/ (имя без расширения), иначе "default".
$headerLayout = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $params->get('header_layout', 'default')) ?: 'default';
$headerFile   = __DIR__ . '/header/' . $headerLayout . '.php';

if (!is_file($headerFile)) {
	$headerFile = __DIR__ . '/header/default.php';
}

$bodyClasses = trim(
	'site'
	. ' wrapper-static'
	. ($option ? ' option-' . $e($option) : '')
	. ($view ? ' view-' . $e($view) : '')
	. ($layout ? ' layout-' . $e($layout) : '')
	. ($task ? ' task-' . $e($task) : '')
	. ($itemid ? ' itemid-' . (int) $itemid : '')
	. ($pageclass ? ' ' . $e($pageclass) : '')
	. ($sticky ? ' has-sticky-header' : '')
	. ($this->direction === 'rtl' ? ' rtl' : '')
);
?>
<!DOCTYPE html>
<html lang="<?php echo $e($this->language); ?>" dir="<?php echo $e($this->direction); ?>">
<head>
	<jdoc:include type="metas" />
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
</head>
<body id="top" class="<?php echo $bodyClasses; ?>">
	<a class="visually-hidden-focusable dharma-skip-link" href="#main-content"><?php echo Text::_('TPL_DHARMA_STARTER_SKIP_TO_CONTENT'); ?></a>

	<?php include $headerFile; ?>

	<?php if ($this->countModules('banner', true)) : ?>
		<div class="dharma-banner"><jdoc:include type="modules" name="banner" style="none" /></div>
	<?php endif; ?>

	<div class="dharma-content">
		<div class="container">
			<jdoc:include type="modules" name="breadcrumbs" style="none" />
			<jdoc:include type="modules" name="main-top" style="none" />
			<jdoc:include type="message" />

			<div class="row g-4">
				<?php if ($this->countModules('sidebar-left', true)) : ?>
					<aside class="col-lg-3"><jdoc:include type="modules" name="sidebar-left" style="card" /></aside>
				<?php endif; ?>
				<main class="col" id="main-content"><jdoc:include type="component" /></main>
				<?php if ($this->countModules('sidebar-right', true)) : ?>
					<aside class="col-lg-3"><jdoc:include type="modules" name="sidebar-right" style="card" /></aside>
				<?php endif; ?>
			</div>

			<jdoc:include type="modules" name="main-bottom" style="none" />
		</div>
	</div>

	<?php
	// Секции: свободные позиции для блоков страниц (главная и др.).
	// Выводятся с обёрткой хрома секции (style="section", html/layouts/chromes/section.php):
	// оборачиваются в .container с теми же боковыми отступами, что и в шапке/контенте,
	// и отображают заголовок модуля при включённой опции «Показать заголовок».
	// Для полноширинных модулей укажите суффикс класса «full».
	foreach (['section-1', 'section-2', 'section-3', 'section-4'] as $sectionPosition) :
		if ($this->countModules($sectionPosition, true)) : ?>
			<jdoc:include type="modules" name="<?php echo $sectionPosition; ?>" style="section" />
		<?php endif;
	endforeach; ?>

	<footer class="dharma-footer">
		<div class="container"><jdoc:include type="modules" name="footer" style="none" /></div>
	</footer>

	<?php if ($this->countModules('debug', true)) : ?>
		<div class="container"><jdoc:include type="modules" name="debug" style="none" /></div>
	<?php endif; ?>
</body>
</html>
