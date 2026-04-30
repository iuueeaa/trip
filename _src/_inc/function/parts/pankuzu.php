<?php

/**
 * $menu_listツリー内でslug一致のメニュー配列を返す（children含めて再帰探索）
 */
function getMenuItemRecursive($menu, $search_slug) {
    foreach ($menu as $slug => $item) {
        if ($slug === $search_slug) return $item;
        if (!empty($item['children'])) {
            $found = getMenuItemRecursive($item['children'], $search_slug);
            if ($found) return $found;
        }
    }
    return null;
}

/**
 * 指定slugの祖先slugチェーン（home→…→parent）を取得
 * @param array $menu
 * @param string $target_slug
 * @return array 例: ['home','company','history']
 */
function getAncestorSlugs($menu, $target_slug) {
    foreach ($menu as $slug => $item) {
        if ($slug === $target_slug) {
            return [$slug];
        }
        if (!empty($item['children'])) {
            $found = getAncestorSlugs($item['children'], $target_slug);
            if (!empty($found)) {
                array_unshift($found, $slug);
                return $found;
            }
        }
    }
    return [];
}

/**
 * パンクズリストの配列を返す
 * @param array $thisPageValue
 * @return array [ [title, url], ... , [title, null] ]
 */
function getPankuzuArray($thisPageValue) {
    global $menu_list, $setting_title_preference;
    $setting = $setting_title_preference['pankuzu'] ?? 'main';

    // 1. 親slugを起点にツリー探索
    $parent_slug = $thisPageValue['parent'] ?? null;

    $slug_chain = [];
    if ($parent_slug) {
        $slug_chain = getAncestorSlugs($menu_list, $parent_slug);
    }

    // 必ずhomeから始まる（slug_chainにhomeが含まれてなければ追加）
    if (!in_array('home', $slug_chain) && isset($menu_list['home'])) {
        array_unshift($slug_chain, 'home');
    }

    // 2. slug→title/urlに変換
    $items = [];
    foreach ($slug_chain as $slug) {
        $item = getMenuItemRecursive($menu_list, $slug);
        if ($item) {
            $title = getPageTitle($item, 'pankuzu');
            $items[] = [
                'title' => $title,
                'url'   => $item['url'] ?? null,
            ];
        }
    }

    // 3. 自分自身（オーバーライド優先）
    $selfTitle = getPageTitle($thisPageValue, 'pankuzu');
    $items[] = [
        'title' => $selfTitle,
        'url'   => null,
    ];

    return $items;
}

/**
 * パンくずHTML出力
 */
function setHtmlPankuzu($thisPageValue) {
    if (!empty($thisPageValue["parent"])):
        $items = getPankuzuArray($thisPageValue);
?>
        <ol class="p-pankuzu">
            <?php foreach ($items as $i => $item): ?>
                <?php if (!empty($item['url']) && $i !== count($items) - 1): ?>
                    <li>
                        <a href="<?= htmlspecialchars($item['url']) ?>">
                            <span><?= htmlspecialchars(strip_tags((string)$item['title'])) ?></span>
                        </a>
                    </li>
                <?php else: ?>
                    <li>
                        <div><span><?= htmlspecialchars(strip_tags((string)$item['title'])) ?></span></div>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
<?php
    endif;
}

/**
 * パンくず構造化データ(JSON-LD)出力
 */
function setJsonPankuzu($thisPageValue) {
    $items = getPankuzuArray($thisPageValue);
    $url = $thisPageValue['nav']['url'] ?? '';

    $itemList = [];
    foreach ($items as $i => $item) {
        $isLast = ($i === count($items) - 1);
        $itemList[] = [
            "@type" => "ListItem",
            "position" => $i + 1,
            "item" => [
                "@id" => $isLast ? $url : $item['url'],
                "name" => $item['title']
            ]
        ];
    }
    $jsonLd = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => $itemList
    ];
    echo '<script type="application/ld+json">' . json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}
