<?php
/*
 * @package     RadicalMart Package
 * @subpackage  com_radicalmart
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Component\RadicalMart\Administrator\Helper\ParamsHelper;
use Joomla\Component\RadicalMart\Site\Helper\RouteHelper;

$app       = Factory::getApplication();
$input     = $app->getInput();
$hideTotal = ($input->get('option') === 'com_radicalmart'
	&& in_array($input->get('view'), ['checkout', 'cart']));
if (!$hideTotal)
{
	// Load assets
	/** @var \Joomla\CMS\WebAsset\WebAssetManager $assets */
	$assets = Factory::getApplication()->getDocument()->getWebAssetManager();
	$assets->getRegistry()->addExtensionRegistryFile('com_radicalmart');
	$assets->useScript('com_radicalmart.site.cart');

	$params = ParamsHelper::getComponentParams();
	if ($params->get('radicalmart_js', 1))
	{
		$assets->useScript('com_radicalmart.site')
			->useScript('bootstrap.toast')
			->useScript('bootstrap.offcanvas');
	}

	if ($params->get('trigger_js', 1))
	{
		$assets->useScript('com_radicalmart.site.trigger');
	}
}
?>
<div>
    <?php if ($hideTotal): ?>
        <a href="<?php echo Route::_(RouteHelper::getCartRoute(), false); ?>" class="dharma-header__cart-link" aria-label="Корзина">
            <span class="ico-cart" aria-hidden="true"></span>
            <span class="dharma-header__cart-badge quantity" radicalmart-cart-display="total.products">0</span>
        </a>
    <?php else: ?>
        <button type="button" radicalmart-cart="display_module" class="dharma-header__cart-link radicalmart-icon" disabled aria-disabled="true" aria-label="Корзина">
            <span class="ico-cart" aria-hidden="true"></span>
            <span class="dharma-header__cart-badge quantity" radicalmart-cart-display="total.products">0</span>
        </button>
    <?php endif; ?>
</div>
