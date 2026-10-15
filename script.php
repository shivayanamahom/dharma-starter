<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

/**
 * Joomla ищет класс по имени <элемент>InstallerScript; у шаблона элемент —
 * просто имя шаблона (без префикса tpl_), иначе скрипт не запускается.
 */
class dharma_starterInstallerScript
{
	public function postflight($type, $parent): void
	{
		if (!in_array($type, ['install', 'update'], true)) {
			return;
		}

		$this->copyBrandingFiles($parent);
		$this->createMenuModule();
	}

	private function copyBrandingFiles($parent): void
	{
		$source = $parent->getParent()->getPath('source') . '/images';
		$target = JPATH_ROOT . '/images/dharma_starter';

		if (!is_dir($target)) {
			@mkdir($target, 0755, true);
		}

		foreach (['logo.png', 'favicon.svg'] as $file) {
			$from = $source . '/' . $file;
			$to   = $target . '/' . $file;

			if (is_file($from) && !is_file($to)) {
				@copy($from, $to);
			}
		}
	}

	/**
	 * Главное меню в позицию "menu": одно меню рисуется и верхней плашкой, и
	 * (клоном с макетом offcanvas) в оффканвасе — шаблон ничего больше не требует.
	 * Берём первый тип меню сайта (на чистой Joomla это "mainmenu"), ставим модуль
	 * на все страницы. Если в позиции "menu" уже есть хоть один модуль
	 * (переустановка/обновление, ручная настройка), ничего не трогаем.
	 */
	private function createMenuModule(): void
	{
		/** @var DatabaseInterface $db */
		$db = Factory::getContainer()->get(DatabaseInterface::class);

		$position = 'menu';
		$query    = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__modules'))
			->where($db->quoteName('client_id') . ' = 0')
			->where($db->quoteName('position') . ' = :position')
			->bind(':position', $position);

		if ((int) $db->setQuery($query)->loadResult() > 0) {
			return;
		}

		$query = $db->getQuery(true)
			->select($db->quoteName('menutype'))
			->from($db->quoteName('#__menu_types'))
			->where($db->quoteName('client_id') . ' = 0')
			->order($db->quoteName('id') . ' ASC');

		$menutype = (string) $db->setQuery($query, 0, 1)->loadResult();

		if ($menutype === '') {
			return;
		}

		$params = json_encode([
			'menutype'        => $menutype,
			'startLevel'      => '1',
			'endLevel'        => '0',
			'showAllChildren' => '1',
			'tag_id'          => '',
			'class_sfx'       => '',
			'window_open'     => '',
			'layout'          => '_:default',
			'moduleclass_sfx' => '',
			'cache'           => '1',
			'cache_time'      => '900',
			'cachemode'       => 'itemid',
		]);

		$module = (object) [
			'title'     => 'Главное меню',
			'note'      => '',
			'content'   => '',
			'ordering'  => 1,
			'position'  => $position,
			'published' => 1,
			'module'    => 'mod_menu',
			'access'    => 1,
			'showtitle' => 0,
			'params'    => $params,
			'client_id' => 0,
			'language'  => '*',
		];

		$db->insertObject('#__modules', $module);

		$link = (object) ['moduleid' => (int) $db->insertid(), 'menuid' => 0];

		$db->insertObject('#__modules_menu', $link);
	}
}
