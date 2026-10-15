<?php

/**
 * Универсальная страница ошибки шаблона Dharma Starter (404, 403, 500 и т. д.).
 * Приложение ошибок не рисует обычный макет сайта, поэтому страница самодостаточна:
 * логотип берётся из параметров шаблона, тексты — из языковых файлов шаблона,
 * вид — из тех же CSS-переменных (--dharma-*), что и весь сайт.
 *
 * @package     Joomla.Site
 * @subpackage  Templates.dharma_starter
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Document\ErrorDocument $this */

$app = Factory::getApplication();

// Языковые файлы шаблона для страницы ошибки подгружаем явно.
$app->getLanguage()->load('tpl_dharma_starter', JPATH_THEMES . '/' . $app->getTemplate());

$wa = $this->getWebAssetManager();

// Font Awesome нужен иконкам ядра Joomla (логин, пагинация и т. п.); шаблон рисует свои иконки сам.
// Пресет Cassiopeia тянет его как зависимость, а убрать зависимость нельзя, поэтому
// при выключенной опции перерегистрируем тот же файл CSS без зависимостей.
$cassiopeiaStyle = $this->direction === 'rtl' ? 'template.cassiopeia.rtl' : 'template.cassiopeia.ltr';

if ((int) $this->params->get('load_fontawesome', 1) !== 1) {
	$wa->registerStyle(
		$cassiopeiaStyle,
		'media/templates/site/cassiopeia/css/' . ($this->direction === 'rtl' ? 'template-rtl.min.css' : 'template.min.css')
	);
}

$wa->usePreset('template.cassiopeia.' . ($this->direction === 'rtl' ? 'rtl' : 'ltr'))
	->useStyle('template.active.language')
	->useStyle('template.dharma.fonts')
	->useStyle('template.dharma.user');

$params = $this->params;

// Поле "media" хранит значение вида images/x.png#joomlaImage://local-images/x.png?width=..;
// оставляем только путь. Старые значения (просто путь) тоже подходят.
$cleanMedia = static fn (string $value): string => ltrim((string) strtok((string) HTMLHelper::cleanImageURL($value)->url, '#'), '/');

$faviconTypes = ['svg' => 'image/svg+xml', 'png' => 'image/png', 'ico' => 'image/x-icon', 'gif' => 'image/gif', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'webp' => 'image/webp', 'avif' => 'image/avif'];

$faviconPath = $cleanMedia((string) $params->get('favicon_path', 'images/dharma_starter/favicon.svg')) ?: 'images/dharma_starter/favicon.svg';
$faviconType = $faviconTypes[strtolower((string) pathinfo($faviconPath, PATHINFO_EXTENSION))] ?? 'image/svg+xml';

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');
$this->addHeadLink(Uri::root(true) . '/' . $faviconPath, 'icon', 'rel', ['type' => $faviconType]);

$errorCode  = (int) $this->error->getCode() ?: 500;
$isNotFound = $errorCode === 404;
$rootUrl    = rtrim(Uri::root(true), '/');
$homeUrl    = $rootUrl . '/';

$logoPath  = $cleanMedia((string) $params->get('logo_path', 'images/dharma_starter/logo.png')) ?: 'images/dharma_starter/logo.png';
$brandName = trim((string) $params->get('brand_name', 'Dharma'));
$logoAlt   = htmlspecialchars($brandName !== '' ? $brandName : 'Dharma', ENT_QUOTES, 'UTF-8');

$kind = $isNotFound ? 'NOT_FOUND' : 'ERROR';
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($this->language, ENT_QUOTES, 'UTF-8'); ?>" dir="<?php echo htmlspecialchars($this->direction, ENT_QUOTES, 'UTF-8'); ?>">
<head>
	<jdoc:include type="metas" />
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
</head>
<body class="site error_site dharma-error-body">
	<main class="dharma-error">
		<div class="dharma-error__shell">
			<a class="dharma-error__brand" href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?php echo $logoAlt; ?>">
				<img src="<?php echo htmlspecialchars($rootUrl . '/' . $logoPath, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo $logoAlt; ?>" width="238" height="48" loading="eager" decoding="async">
			</a>

			<div class="dharma-error__layout">
				<section class="dharma-error__content" aria-labelledby="dharma-error-title">
					<div class="dharma-error__eyebrow"><span></span><?php echo Text::_('TPL_DHARMA_STARTER_ERROR_' . $kind . '_EYEBROW'); ?></div>

					<h1 id="dharma-error-title"><?php echo Text::_('TPL_DHARMA_STARTER_ERROR_' . $kind . '_TITLE'); ?></h1>
					<p class="dharma-error__description"><?php echo Text::_('TPL_DHARMA_STARTER_ERROR_' . $kind . '_TEXT'); ?></p>

					<div class="dharma-error__actions">
						<a class="knopka ripple-hover" href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>">
							<?php echo Text::_('TPL_DHARMA_STARTER_ERROR_HOME'); ?> <span class="ar-arrow" aria-hidden="true"></span>
						</a>
						<?php if (!$isNotFound) : ?>
							<a class="knopka knopka2 ripple-hover" href="<?php echo htmlspecialchars(Uri::getInstance()->toString(['path', 'query']), ENT_QUOTES, 'UTF-8'); ?>">
								<?php echo Text::_('TPL_DHARMA_STARTER_ERROR_RELOAD'); ?>
							</a>
						<?php endif; ?>
					</div>
				</section>

				<div class="dharma-error__visual" aria-hidden="true">
					<div class="dharma-error__grid"></div>
					<strong class="dharma-error__code"><?php echo $errorCode; ?></strong>
				</div>
			</div>
		</div>

		<?php if ($this->debug && !$isNotFound) : ?>
			<details class="dharma-error__debug">
				<summary><?php echo Text::_('TPL_DHARMA_STARTER_ERROR_DEBUG'); ?></summary>
				<?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?>
				<?php echo $this->renderBacktrace(); ?>
			</details>
		<?php endif; ?>
	</main>
</body>
</html>
