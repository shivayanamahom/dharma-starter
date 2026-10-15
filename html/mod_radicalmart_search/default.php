<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.dharma_starter
 */

defined('_JEXEC') or die;

/**
 * Template variables
 * -----------------
 *
 * @var  object   $module Module object
 * @var  Registry $params Module params
 */

$placeholder = $params->get('placeholder', 'Поиск по каталогу продукции');
$minChars    = (int) $params->get('min_chars', 2);
$delay       = (int) $params->get('delay', 300);
$moduleId    = $module->id;
$catalogUrl  = $catalog_url ?? '';
$ajaxUrl     = \Joomla\CMS\Uri\Uri::root(true) . '/index.php?option=com_ajax&module=radicalmart_search&method=get&format=json&limit='
	. min(20, max(1, (int) $params->get('max_results', 10)));
// Безопасная вставка значений в <script>: теги, амперсанд и кавычки кодируются.
$jsonFlags   = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
?>
<div class="radicalmart-search-wrapper">
    <div class="dharma-header__search-group position-relative">
        <input
            type="text"
            id="radicalmart-search-input-<?php echo $moduleId; ?>"
            class="form-control dharma-header__search-input"
            placeholder="<?php echo htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8'); ?>"
            autocomplete="off"
        />
        <button type="button" class="dharma-header__search-submit" id="radicalmart-search-btn-<?php echo $moduleId; ?>" aria-label="Найти">
            <span class="ico-search" aria-hidden="true"></span>
        </button>

        <div id="radicalmart-search-results-<?php echo $moduleId; ?>" class="search-autocomplete-results"></div>
    </div>
</div>

<script>
(function() {
	'use strict';

	const moduleId = '<?php echo $moduleId; ?>';
	const minChars = <?php echo $minChars; ?>;
	const delay = <?php echo $delay; ?>;
	const ajaxUrl = <?php echo json_encode($ajaxUrl, $jsonFlags); ?>;

	const searchInput = document.getElementById('radicalmart-search-input-' + moduleId);
	const searchBtn = document.getElementById('radicalmart-search-btn-' + moduleId);
	const resultsContainer = document.getElementById('radicalmart-search-results-' + moduleId);

	if (!searchInput || !resultsContainer) {
		return;
	}

	let searchTimeout;
	let activeRequest;

	function searchProducts(query) {
		if (query.length < minChars) {
			resultsContainer.classList.remove('show');
			return;
		}

		resultsContainer.innerHTML = '<div class="search-autocomplete-loading">Поиск...</div>';
		resultsContainer.classList.add('show');

		if (activeRequest) {
			activeRequest.abort();
		}

		activeRequest = new AbortController();
		const url = ajaxUrl + '&query=' + encodeURIComponent(query);

		fetch(url, { signal: activeRequest.signal })
			.then(response => {
				if (!response.ok) {
					throw new Error('HTTP error ' + response.status);
				}
				return response.json();
			})
			.then(response => {
				const data = response.data || response;

				if (data.success && data.items) {
					displayResults(data);
				} else {
					// Текст ошибки сервера посетителю не показываем.
					resultsContainer.innerHTML = '<div class="search-autocomplete-no-results">Ошибка при поиске</div>';
				}
			})
			.catch(error => {
				if (error.name === 'AbortError') {
					return;
				}
				resultsContainer.innerHTML = '<div class="search-autocomplete-no-results">Ошибка при поиске</div>';
			});
	}

	function submitSearch(query) {
		const catalogUrl = <?php echo json_encode($catalogUrl, $jsonFlags); ?>;
		if (!catalogUrl) {
			return;
		}

		const fallbackToCatalog = function() {
			const separator = catalogUrl.indexOf('?') !== -1 ? '&' : '?';
			window.location.href = catalogUrl + separator + 'filter[search]=' + encodeURIComponent(query);
		};
		const url = ajaxUrl + '&query=' + encodeURIComponent(query);

		fetch(url)
			.then(response => response.ok ? response.json() : Promise.reject())
			.then(response => {
				const data = response.data || response;
				const exactItem = data.items && data.items.find(item => item.code_exact && item.link);

				if (exactItem && safeUrl(exactItem.link)) {
					window.location.href = safeUrl(exactItem.link);
					return;
				}

				fallbackToCatalog();
			})
			.catch(fallbackToCatalog);
	}

	function displayResults(data) {
		if (!data || !data.items || data.items.length === 0) {
			resultsContainer.innerHTML = '<div class="search-autocomplete-no-results">Ничего не найдено</div>';
			return;
		}

		let html = '';
		data.items.forEach(function(item) {
			const imageUrl = safeUrl(item.image || '');
			const price = item.price || '';
			const priceFormatted = item.price_string ? String(item.price_string) : (price ? formatNumber(price) + ' ₽' : '');

			// Все значения экранируются: ссылка и картинка идут в атрибуты.
			html += '<a href="' + escapeHtml(safeUrl(item.link || '#') || '#') + '" class="search-autocomplete-item text-decoration-none">';
			html += imageUrl ? '<img src="' + escapeHtml(imageUrl) + '" alt="' + escapeHtml(item.title) + '" />' : '';
			html += '<div class="search-autocomplete-item-info">';
			html += '<div class="search-autocomplete-item-title">' + escapeHtml(item.title) + '</div>';
			html += item.code ? '<div class="search-autocomplete-item-code">Артикул: ' + escapeHtml(item.code) + '</div>' : '';
			html += priceFormatted ? '<div class="search-autocomplete-item-price">' + escapeHtml(priceFormatted) + '</div>' : '';
			html += '</div>';
			html += '</a>';
		});

		resultsContainer.innerHTML = html;
	}

	function formatNumber(num) {
		return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
	}

	function escapeHtml(text) {
		return String(text === null || text === undefined ? '' : text)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	}

	// Допускаем только http(s) и адреса от корня сайта; javascript:, data: и //host отбрасываем.
	function safeUrl(url) {
		url = String(url || '').trim();
		return /^(https?:\/\/|\/(?!\/))/i.test(url) ? url : '';
	}

	searchInput.addEventListener('input', function(e) {
		clearTimeout(searchTimeout);
		const query = e.target.value.trim();

		searchTimeout = setTimeout(function() {
			searchProducts(query);
		}, delay);
	});

	document.addEventListener('click', function(e) {
		if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
			resultsContainer.classList.remove('show');
		}
	});

	searchInput.addEventListener('focus', function() {
		if (this.value.trim().length >= minChars && resultsContainer.innerHTML) {
			resultsContainer.classList.add('show');
		}
	});

	searchBtn.addEventListener('click', function() {
		const query = searchInput.value.trim();
		if (query.length >= minChars) {
			submitSearch(query);
		}
	});

	searchInput.addEventListener('keypress', function(e) {
		if (e.key === 'Enter') {
			e.preventDefault();
			searchBtn.click();
		}
	});
})();
</script>
