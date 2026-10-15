<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * Separate mobile menu layout for the Parmaled offcanvas.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;

$tagId      = $params->get('tag_id', '') ?: 'mobile-menu' . $module->id;
$id         = ' id="' . htmlspecialchars($tagId, ENT_QUOTES, 'UTF-8') . '"';
$startLevel = (int) $params->get('startLevel', 1);
$submenuIndex = 0;
?>
<ul<?php echo $id; ?> class="mod-menu mod-menu_offcanvas mod-list <?php echo $class_sfx; ?>">
<?php foreach ($list as $item) :
    if (!$showAll && $item->level > $startLevel) {
        continue;
    }

    $itemParams = $item->getParams();
    $class      = ['menu-item item-' . $item->id];

    if ($item->id == $default_id) {
        $class[] = 'default';
    }

    if ($item->id == $active_id || ($item->type === 'alias' && $itemParams->get('aliasoptions') == $active_id)) {
        $class[] = 'current';
    }

    if (in_array($item->id, $path)) {
        $class[] = 'active';
    } elseif ($item->type === 'alias') {
        $aliasToId = $itemParams->get('aliasoptions');

        if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
            $class[] = 'alias-parent-active';
        } elseif (in_array($aliasToId, $path)) {
            $class[] = 'alias-parent-active';
        }
    }

    if ($item->type === 'separator') {
        $class[] = 'divider';
    }

    if ($item->deeper) {
        $class[] = 'deeper';
    }

    if ($item->parent) {
        $class[] = 'parent';
    }

    $isOpen = $item->deeper && in_array($item->id, $path) && $item->id != $active_id;

    echo '<li class="' . htmlspecialchars(implode(' ', $class), ENT_QUOTES, 'UTF-8') . '">';

    switch ($item->type) :
        case 'separator':
        case 'component':
        case 'heading':
        case 'url':
            require ModuleHelper::getLayoutPath('mod_menu', 'default_' . $item->type);
            break;

        default:
            require ModuleHelper::getLayoutPath('mod_menu', 'default_url');
            break;
    endswitch;

    if ($item->deeper) :
        $submenuId = 'mobile-menu-' . $module->id . '-submenu-' . $submenuIndex++;
        $expanded  = $isOpen ? 'true' : 'false';
        $state     = $isOpen ? ' show' : '';
        $label     = htmlspecialchars(Text::sprintf('MOD_MENU_TOGGLE_SUBMENU_LABEL', $item->title), ENT_QUOTES, 'UTF-8');
        ?>
        <button type="button"
            class="mod-menu__toggle-sub<?php echo $isOpen ? '' : ' collapsed'; ?>"
            data-bs-toggle="collapse"
            data-bs-target="#<?php echo htmlspecialchars($submenuId, ENT_QUOTES, 'UTF-8'); ?>"
            aria-expanded="<?php echo $expanded; ?>"
            aria-controls="<?php echo htmlspecialchars($submenuId, ENT_QUOTES, 'UTF-8'); ?>">
            <span class="icon-chevron-down" aria-hidden="true"></span>
            <span class="visually-hidden"><?php echo $label; ?></span>
        </button>
        <ul id="<?php echo htmlspecialchars($submenuId, ENT_QUOTES, 'UTF-8'); ?>" class="mod-menu__sub list-unstyled collapse<?php echo $state; ?>">
        <?php
    elseif ($item->shallower) :
        echo '</li>' . str_repeat('</ul></li>', $item->level_diff);
    else :
        echo '</li>';
    endif;
endforeach;
?></ul>
