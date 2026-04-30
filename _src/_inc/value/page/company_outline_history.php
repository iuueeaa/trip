<?php
$p_key = "company_outline_history";
${'page_' . $p_key} = defaultPageValue($p_key, array(
	'title' => setValueTitle('会社沿革', "History"), //mainが日, subが英
	'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
	'text'  => '',
));

registerAcfFromValue($p_key, '会社沿革', 'page', ${'page_' . $p_key});
