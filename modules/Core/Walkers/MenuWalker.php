<?php
	namespace Modules\Core\Walkers;
	class MenuWalker
	{
		protected static $currentMenuItem;
		protected        $menu;
		protected $activeItems = [];

		public function __construct($menu)
		{
			$this->menu = $menu;
		}

		public function generate()
		{
			$items = json_decode($this->menu->items, true);
			if (!empty($items)) {
				echo '<ul class="main-menu menu-generated menu-with-icons">';
				$this->generateTree($items);
				echo '</ul>';
			}
		}

		public function generateTree($items = [],$depth = 0,$parentKey = '')
		{

			foreach ($items as $k=>$item) {

				$class = e($item['class'] ?? '');
				$url = $item['url'] ?? '';
				$item['target'] = $item['target'] ?? '';
				if (!isset($item['item_model']))
					continue;
				if (class_exists($item['item_model'])) {
					$itemClass = $item['item_model'];
					$itemObj = $itemClass::find($item['id']);
					if (empty($itemObj)) {
						continue;
					}
					$url = $itemObj->getDetailUrl();
				}
				if ($this->checkCurrentMenu($item, $url))
				{
					$class .= ' active';
					$this->activeItems[] = $parentKey;
				}

				if (!empty($item['children'])) {
					ob_start();
					$this->generateTree($item['children'],$depth + 1,$parentKey.'_'.$k);
					$html = ob_get_clean();
					if(in_array($parentKey.'_'.$k,$this->activeItems)){
						$class.=' active ';
					}
				}
				$class.=' depth-'.($depth);
				if ($depth === 0) {
					$iconTone = $this->resolveIconTone($item, $url);
					if ($iconTone) {
						$class .= ' menu-icon-tone-' . $iconTone;
					}
				}
				$hasChildren = !empty($item['children']);
				printf('<li class="%s">', $class);
				printf('<a target="%s" href="%s">', e($item['target']), e($url));
				$this->renderMenuLinkContent($item, $url, $depth, $hasChildren);
				echo '</a>';
				if ($hasChildren) {
					echo '<ul class="children-menu menu-dropdown">';
					echo $html;
					echo "</ul>";
				}
				echo '</li>';
			}
		}

		protected function renderMenuLinkContent(array $item, string $url, int $depth, bool $hasChildren = false): void
		{
			$label = $item['name'] ?? '';
			$iconClass = $this->resolveIcon($item, $url);

			if ($depth === 0 && $iconClass) {
				echo '<span class="menu-item-icon"><i class="' . e($iconClass) . '" aria-hidden="true"></i></span>';
				echo '<span class="menu-item-label">' . clean($label) . '</span>';
			} else {
				echo clean($label);
			}

			if ($hasChildren) {
				echo ' <i class="caret fa fa-angle-down" aria-hidden="true"></i>';
			}
		}

		protected function resolveIcon(array $item, string $url): string
		{
			if (!empty($item['icon'])) {
				return trim($item['icon']);
			}

			$path = trim(parse_url($url, PHP_URL_PATH) ?: $url, '/');
			if ($path === '' || $path === '#') {
				$name = strtolower(strip_tags($item['name'] ?? ''));
				return $this->resolveIconByName($name) ?: 'fa fa-link';
			}

			$slug = strtolower(basename($path));

			$map = [
				'home'             => 'fa fa-home',
				'stay'             => 'fa fa-bed',
				'tour'             => 'fa fa-suitcase',
				'travel-agent'     => 'fa fa-id-card-o',
				'tourist-vehicle'  => 'fa fa-bus',
				'event'            => 'fa fa-ticket',
				'tour-itinerary'   => 'fa fa-map-o',
				'boat'             => 'fa fa-ship',
				'plan'             => 'fa fa-calendar-check-o',
				'contact'          => 'fa fa-envelope-o',
				'page'             => 'fa fa-file-text-o',
				'become-a-vendor'  => 'fa fa-briefcase',
				'club-member'      => 'fa fa-users',
			];

			if (isset($map[$path])) {
				return $map[$path];
			}
			if (isset($map[$slug])) {
				return $map[$slug];
			}

			$name = strtolower(strip_tags($item['name'] ?? ''));

			return $this->resolveIconByName($name) ?: 'fa fa-link';
		}

		protected function resolveIconTone(array $item, string $url): string
		{
			$path = trim(parse_url($url, PHP_URL_PATH) ?: $url, '/');
			if ($path === '' || $path === '#') {
				$name = strtolower(strip_tags($item['name'] ?? ''));
				return $this->resolveIconToneByName($name) ?: 'default';
			}

			$slug = strtolower(basename($path));

			$map = [
				'home'             => 'home',
				'stay'             => 'stay',
				'tour'             => 'tour',
				'travel-agent'     => 'agent',
				'tourist-vehicle'  => 'vehicle',
				'event'            => 'event',
				'tour-itinerary'   => 'itinerary',
				'boat'             => 'boat',
				'plan'             => 'plan',
				'contact'          => 'contact',
				'page'             => 'page',
				'become-a-vendor'  => 'vendor',
				'club-member'      => 'member',
			];

			if (isset($map[$path])) {
				return $map[$path];
			}
			if (isset($map[$slug])) {
				return $map[$slug];
			}

			$name = strtolower(strip_tags($item['name'] ?? ''));

			return $this->resolveIconToneByName($name) ?: 'default';
		}

		protected function resolveIconToneByName(string $name): ?string
		{
			if ($name === '') {
				return null;
			}
			if (str_contains($name, 'home')) {
				return 'home';
			}
			if (str_contains($name, 'hotel') || str_contains($name, 'stay')) {
				return 'stay';
			}
			if (str_contains($name, 'tour') && !str_contains($name, 'itinerary')) {
				return 'tour';
			}
			if (str_contains($name, 'travel') || str_contains($name, 'agent')) {
				return 'agent';
			}
			if (str_contains($name, 'vehicle') || str_contains($name, 'bus') || str_contains($name, 'cab')) {
				return 'vehicle';
			}
			if (str_contains($name, 'event') || str_contains($name, 'park')) {
				return 'event';
			}
			if (str_contains($name, 'boat') || str_contains($name, 'cruise')) {
				return 'boat';
			}
			if (str_contains($name, 'itinerary') || str_contains($name, 'map')) {
				return 'itinerary';
			}
			if (str_contains($name, 'portfolio') || str_contains($name, 'protfolio')) {
				return 'portfolio';
			}
			if (str_contains($name, 'contact')) {
				return 'contact';
			}
			if (str_contains($name, 'plan')) {
				return 'plan';
			}
			if (str_contains($name, 'vendor')) {
				return 'vendor';
			}
			if (str_contains($name, 'member')) {
				return 'member';
			}

			return null;
		}

		protected function resolveIconByName(string $name): ?string
		{
			if ($name === '') {
				return null;
			}
			if (str_contains($name, 'home')) {
				return 'fa fa-home';
			}
			if (str_contains($name, 'hotel') || str_contains($name, 'stay')) {
				return 'fa fa-bed';
			}
			if (str_contains($name, 'tour')) {
				return 'fa fa-suitcase';
			}
			if (str_contains($name, 'travel') || str_contains($name, 'agent')) {
				return 'fa fa-id-card-o';
			}
			if (str_contains($name, 'vehicle') || str_contains($name, 'bus') || str_contains($name, 'cab')) {
				return 'fa fa-bus';
			}
			if (str_contains($name, 'event') || str_contains($name, 'park')) {
				return 'fa fa-ticket';
			}
			if (str_contains($name, 'boat') || str_contains($name, 'cruise')) {
				return 'fa fa-ship';
			}
			if (str_contains($name, 'itinerary') || str_contains($name, 'map')) {
				return 'fa fa-map-o';
			}
			if (str_contains($name, 'portfolio') || str_contains($name, 'protfolio')) {
				return 'fa fa-th-large';
			}
			if (str_contains($name, 'contact')) {
				return 'fa fa-envelope-o';
			}
			if (str_contains($name, 'plan')) {
				return 'fa fa-calendar-check-o';
			}
			if (str_contains($name, 'member') || str_contains($name, 'vendor')) {
				return 'fa fa-users';
			}

			return null;
		}

		protected function checkCurrentMenu($item, $url = '')
		{

			if(trim($url,'/') == request()->path()){
				return true;
			}
			if (!static::$currentMenuItem)
				return false;
			if (empty($item['item_model']))
				return false;
			if (is_string(static::$currentMenuItem) and ($url == static::$currentMenuItem or $url == url(static::$currentMenuItem))) {
				return true;
			}
			if (is_object(static::$currentMenuItem) and get_class(static::$currentMenuItem) == $item['item_model'] && static::$currentMenuItem->id == $item['id']) {
				return true;
			}
			return false;
		}

		public static function setCurrentMenuItem($item)
		{
			static::$currentMenuItem = $item;
		}

		public static function getActiveMenu()
		{
			return static::$currentMenuItem;
		}
	}