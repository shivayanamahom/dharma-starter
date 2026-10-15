<?php

/**
 * Обёртка модуля «section»: модуль становится полноширинной секцией страницы
 * (<section>) с фоном и вертикальными отступами. Включается в index.php для
 * позиций section-N или выбором стиля «section» в настройках модуля.
 *
 * Вид задаётся словами в поле модуля «Суффикс класса модуля» (через пробел):
 *   фон:     soft | gray | primary | dark       (по умолчанию без фона)
 *   отступ:  sm | lg | nopad                    (по умолчанию средний)
 *   ширина:  full — содержимое на всю ширину    (по умолчанию в контейнере)
 * Любые другие слова добавляются как обычные CSS-классы.
 * Пример: «soft lg» — светлый фон и увеличенные отступы.
 *
 * @package     Joomla.Site
 * @subpackage  Templates.dharma_starter
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

$module = $displayData['module'];
$params = $displayData['params'];

if ($module->content === null || $module->content === '') {
	return;
}

$known   = ['soft', 'gray', 'primary', 'dark', 'sm', 'lg', 'nopad', 'full'];
$classes = ['dharma-section', 'dharma-section--' . preg_replace('/[^a-z0-9_-]/i', '', (string) $module->position)];
$full    = false;

foreach (preg_split('/\s+/', trim((string) $params->get('moduleclass_sfx', '')), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $word) {
	$word = preg_replace('/[^A-Za-z0-9_-]/', '', $word);

	if ($word === '') {
		continue;
	}

	$full      = $full || $word === 'full';
	$classes[] = \in_array($word, $known, true) ? 'dharma-section--' . $word : $word;
}

// Тег модуля: «div» — значение по умолчанию в настройках, для секции лучше <section>.
$tags      = ['section', 'div', 'aside', 'article'];
$moduleTag = (string) $params->get('module_tag', 'div');
$moduleTag = ($moduleTag === 'div' || !\in_array($moduleTag, $tags, true)) ? 'section' : $moduleTag;

$headerTag = (string) $params->get('header_tag', 'h2');
$headerTag = \in_array($headerTag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p', 'span'], true) ? $headerTag : 'h2';

$headerAttribs = ['class' => trim('dharma-section__title ' . htmlspecialchars((string) $params->get('header_class', ''), ENT_QUOTES, 'UTF-8'))];
$moduleAttribs = ['class' => implode(' ', $classes)];

// У секции должно быть имя для программ чтения экрана.
if ($module->showtitle) {
	$moduleAttribs['aria-labelledby'] = 'mod-' . (int) $module->id;
	$headerAttribs['id']              = 'mod-' . (int) $module->id;
} else {
	$moduleAttribs['aria-label'] = htmlspecialchars((string) $module->title, ENT_QUOTES, 'UTF-8');
}

$header = $module->showtitle
	? '<' . $headerTag . ' ' . ArrayHelper::toString($headerAttribs) . '>' . $module->title . '</' . $headerTag . '>'
	: '';
?>
<<?php echo $moduleTag; ?> <?php echo ArrayHelper::toString($moduleAttribs); ?>>
	<?php if ($full) : ?>
		<?php if ($header !== '') : ?><div class="container"><?php echo $header; ?></div><?php endif; ?>
		<?php echo $module->content; ?>
	<?php else : ?>
		<div class="container">
			<?php echo $header; ?>
			<?php echo $module->content; ?>
		</div>
	<?php endif; ?>
</<?php echo $moduleTag; ?>>
