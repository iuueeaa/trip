<?php

function setHtmlNav($menu_list, $pos, $class = "nav", $icon = false, $taxonomy_slug = '') {
	global $page_class, $link_path, $sitemapArr;

?>
	<nav class="<?php echo $class; ?>">
		<ul class="<?php echo $class; ?>__ul">
			<?php foreach ($menu_list as $key => $thismenu) : ?>
				<?php
				$nav = $thismenu;
				if (in_array($pos, $nav['display'])) :
					$children = array();

					// sitemap エントリを探し、children が動的設定（type キーあり）か確認
					$sitemapEntry = null;
					if (!empty($sitemapArr) && is_string($key)) {
						foreach ($sitemapArr as $entry) {
							if ($entry['slug'] === $key) {
								$sitemapEntry = $entry;
								break;
							}
						}
					}

					if (
						!empty($sitemapEntry) &&
						!empty($sitemapEntry['children']) &&
						is_array($sitemapEntry['children']) &&
						isset($sitemapEntry['children']['type'])
					) {
						// 動的生成: sitemap.json の children 設定から生成
						$children = generateChildren($key, $nav, $pos, $taxonomy_slug);
					} elseif (!empty($nav['children'])) {
						// 静的 children: display フィルタで絞り込む
						foreach ($nav['children'] as $child) {
							if (in_array($pos, $child['display'])) {
								$children[] = $child;
							}
						}
					}


					$title = $nav["title"];
					$liclass = $class . '__li';
					$liclass .= (!empty($children)) ? ' has-child' : '';
					$liclass .= (strpos($page_class, 'page-' . $key) !== false) ? " is-active" : '';
					if (!empty($nav['class'])) {
						$liclass .= ' ' . $nav['class'];
					}
				?>
					<li class="<?php echo $liclass; ?>">
						<?php
						// aタグの属性を設定
						$aclass = $class . '__link';
						$attrArray = array();
						if (!empty($name)) {
							$attrArray[] = 'name="' . $name . '"';
						}
						if (!empty($nav['target'])) {
							$attrArray[] = 'target="' . $nav['target'] . '"';
							if ($nav['target'] == "_blank") {
								$nav['icon'] = "icon-link";
							}
						}
						$attrArray[] = 'href="' . $nav['url'] . '"';
						if (!empty($nav['modal'])) {
							$attrArray[] = 'data-modal="' . $nav['modal'] . '"';
							$aclass .= ' js-modal__open';
						}

						$attrArray[] = 'class="' . $aclass . '"';
						?>
						<a <?php echo implode(" ", $attrArray); ?>>
							<span class="<?php echo $class; ?>__wrap">
								<?php if (!empty($title['main'])) : ?>
									<span class="<?php echo $class; ?>__title"><?php echo strip_tags($title['main']); ?></span>
								<?php endif; ?>
								<?php if (!empty($title['sub'])) : ?>
									<span class="<?php echo $class; ?>__subtitle"><?php echo strip_tags($title['sub']); ?></span>
								<?php endif; ?>
								<?php if ($icon && !empty($nav['icon'])) : ?>
									<span class="<?php echo $class; ?>__icon"><?php setHtmlSvg($nav['icon']); ?></span>
								<?php endif; ?>
							</span>
						</a>

						<?php
						if (!empty($children)) {
							setHtmlNav($children, $pos, $class . "__child", $icon);
						}
						?>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</nav>
<?php
}

/**
 * sitemap.json の children 設定から動的に子メニューを生成する。
 *
 * type=taxonomy : sitemap エントリの taxonomy.value から children を生成。
 *                 slug が複数の場合は各 taxonomy の先頭にグループ見出しを挿入。
 *                 term の3要素目が同 taxonomy の既存 slug と一致する場合は親子階層として扱う。
 *                 all:true の場合は先頭に「すべて見る」を追加。
 * type=post     : WP 環境では get_posts、非 WP 環境では ${menu_key}_list から取得。
 *
 * @param string $menu_key      メニューキー（sitemap の slug）
 * @param array  $nav           親メニューエントリ（url, display 等を含む）
 * @param string $pos           表示位置（'header', 'modal' 等）
 * @param string $taxonomy_slug 絞り込む taxonomy slug（未指定時は config の全 slug を処理）
 * @return array 生成された子メニュー配列（display フィルタ済み）
 */
function generateChildren($menu_key, $nav, $pos, $taxonomy_slug = '') {
	global $sitemapArr, $root_path, $wpflg;

	// $sitemapArr がまだロードされていない場合はフォールバックロード
	if (empty($sitemapArr)) {
		$file = $root_path . '/assets/files/sitemap.json';
		if (file_exists($file)) {
			$sitemapArr = json_decode(file_get_contents($file), true);
		} else {
			return array();
		}
	}

	// sitemap エントリを取得
	$sitemapEntry = null;
	foreach ($sitemapArr as $entry) {
		if ($entry['slug'] === $menu_key) {
			$sitemapEntry = $entry;
			break;
		}
	}

	if (empty($sitemapEntry) || empty($sitemapEntry['children']) || !isset($sitemapEntry['children']['type'])) {
		return array();
	}

	$config     = $sitemapEntry['children'];
	$type       = $config['type'];
	$hasAll     = !empty($config['all']);
	$parentUrl  = $nav['url'];
	$parentDisplay = $nav['display'];

	$children = array();

	if ($type === 'taxonomy') {
		$configSlugs    = $config['slug'] ?? [];
		$slugsToProcess = (!empty($taxonomy_slug)) ? [$taxonomy_slug] : $configSlugs;
		$showGroupTitle = count($slugsToProcess) > 1;

		// 「すべて見る」リンク
		if ($hasAll) {
			$children[] = array(
				'url'     => $parentUrl,
				'target'  => '',
				'title'   => array('main' => 'すべて見る', 'sub' => ''),
				'icon'    => '',
				'class'   => '',
				'display' => $parentDisplay,
			);
		}

		foreach ($slugsToProcess as $taxSlug) {
			// taxonomy エントリを取得
			$targetTax = null;
			foreach ($sitemapEntry['taxonomy'] as $tax) {
				if ($tax['slug'] === $taxSlug) {
					$targetTax = $tax;
					break;
				}
			}
			if (empty($targetTax) || empty($targetTax['value'])) continue;

			// グループ見出し（taxonomy が複数の場合のみ）
			if ($showGroupTitle) {
				$children[] = array(
					'url'     => '',
					'target'  => '',
					'title'   => array('main' => $targetTax['name'], 'sub' => ''),
					'icon'    => '',
					'class'   => 'is-group-title',
					'display' => $parentDisplay,
				);
			}

			// 親子判定用: この taxonomy に存在する全 slug の一覧
			$allTermSlugs = array_map(function ($v) { return $v[1]; }, $targetTax['value']);

			// フラットリストを生成（parent キーで親子関係を記録）
			$flatItems = array();
			foreach ($targetTax['value'] as $term) {
				$termName = $term[0];
				$termSlug = $term[1];
				$parent   = null;
				// 3要素目が同 taxonomy の既存 slug と一致する場合のみ親子化
				if (isset($term[2]) && in_array($term[2], $allTermSlugs)) {
					$parent = $term[2];
				}
					$flatItems[$termSlug] = array(
						'url'      => buildTaxonomyUrl($menu_key, $taxSlug, $termSlug),
						'target'   => '',
					'title'    => array('main' => $termName, 'sub' => ''),
					'icon'     => '',
					'class'    => '',
					'display'  => $parentDisplay,
					'parent'   => $parent,
					'children' => array(),
				);
			}

			// 親子関係があるか判定
			$hasHierarchy = false;
			foreach ($flatItems as $item) {
				if (!empty($item['parent']) && isset($flatItems[$item['parent']])) {
					$hasHierarchy = true;
					break;
				}
			}

			if ($hasHierarchy) {
				// 子を親の children に格納
				foreach ($flatItems as $termSlug => $item) {
					if (!empty($item['parent']) && isset($flatItems[$item['parent']])) {
						$childEntry = $item;
						unset($childEntry['parent'], $childEntry['children']);
						$flatItems[$item['parent']]['children'][] = $childEntry;
					}
				}
				// 親のみ $children に追加
				foreach ($flatItems as $item) {
					if (empty($item['parent'])) {
						$entry = $item;
						unset($entry['parent']);
						if (!empty($entry['children'])) {
							$entry['class'] = ltrim($entry['class'] . ' has-child');
						}
						$children[] = $entry;
					}
				}
			} else {
				// 階層なし: フラットに追加
				foreach ($flatItems as $item) {
					$entry = $item;
					unset($entry['parent'], $entry['children']);
					$children[] = $entry;
				}
			}
		} // end foreach $slugsToProcess

	} elseif ($type === 'post') {
		// 「すべて見る」リンク
		if ($hasAll) {
			$children[] = array(
				'url'     => $parentUrl,
				'target'  => '',
				'title'   => array('main' => 'すべて見る', 'sub' => ''),
				'icon'    => '',
				'class'   => '',
				'display' => $parentDisplay,
			);
		}

		if (!empty($wpflg)) {
			// WordPress 環境: get_posts で取得
			$wpPosts = get_posts(array(
				'post_type'      => $menu_key,
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			));
			foreach ($wpPosts as $wpPost) {
				$children[] = array(
					'url'     => get_permalink($wpPost),
					'target'  => '',
					'title'   => array('main' => $wpPost->post_title, 'sub' => ''),
					'icon'    => '',
					'class'   => '',
					'display' => $parentDisplay,
				);
			}
		} else {
			// 非 WP 環境: グローバル変数 ${menu_key}_list から取得
			$listVar = $menu_key . '_list';
			global $$listVar;
			$postList = $$listVar ?? [];
			foreach ($postList as $idx => $item) {
				$title = $item['title']['main'] ?? (is_string($item['title'] ?? null) ? $item['title'] : '');
				$children[] = array(
					'url'     => $parentUrl . 'detail.php?id=' . $idx,
					'target'  => '',
					'title'   => array('main' => $title, 'sub' => ''),
					'icon'    => '',
					'class'   => '',
					'display' => $parentDisplay,
				);
			}
		}
	}

	// display フィルタリング
	return array_values(array_filter($children, function ($child) use ($pos) {
		return in_array($pos, $child['display']);
	}));
}
