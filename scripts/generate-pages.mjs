import fs from "fs";
import path from "path";

const ROOT_DIR = process.cwd();

const SITEMAP_PATH = path.join(ROOT_DIR, "_src/files/sitemap.json");
const HTML_DIR = path.join(ROOT_DIR, "_src/html");
const PAGE_DIR = path.join(ROOT_DIR, "_src/_inc/page");
const VALUE_DIR = path.join(ROOT_DIR, "_src/_inc/value/page");
const SCSS_DIR = path.join(ROOT_DIR, "_src/scss/layout/page");
const SCSS_INDEX = path.join(SCSS_DIR, "_index.scss");
const JA_PHP = path.join(ROOT_DIR, "_src/_inc/value/ja.php");

const SKIP_SLUGS = ["home", "_dev", "styleguide", "template"];
const SKIP_POST_TYPES = ["top", "link"];

// ─── テンプレート ────────────────────────────────────────────

function htmlIndexTemplate(depth) {
  const up = "../".repeat(depth);
  return `<?php

$current_dir = getcwd();


require_once("${up}assets/inc/_l-page.php");
`;
}

function htmlDetailTemplate(depth) {
  const up = "../".repeat(depth);
  return `<?php
$current_dir = getcwd();
$post_type_slug = $current_dir;
$GetId = (isset($_GET['id'])) ? $_GET['id'] : 0;
require_once("${up}assets/inc/_l-page.php");
`;
}

function pageTemplate(slug, name) {
  return `<?php setHtmlMv($this_page_value, 'b-mv__sub'); ?>

<?php
$sectionId = "main";
$sectionClass = $this_page_value['class'] . "__" . $sectionId;
$sectionValue = $this_page_value[$sectionId] ?? [];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
  <div class="section__wrap">
    <div class="<?php echo $sectionClass; ?>__wrap">
      <?php setHtmlTitle($sectionValue["title"] ?? null, "p-title__sec", "h2"); ?>
      <?php setHtmlText($sectionValue["text"] ?? null); ?>
    </div>
  </div>
</section>
`;
}

function pageArchiveTemplate(slug, name) {
  return `<?php
setHtmlMv($this_page_value, 'b-mv__sub');
?>

<?php
$sectionId = "index";
$sectionClass = "page-${slug}__" . $sectionId;
$sectionValue = $this_page_value;
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
  <div class="section__wrap">
    <div class="<?php echo $sectionClass; ?>__wrap">
      <?php
      $blockClass = 'b-list__col3';
      $blockValue = getPostListArray($sectionValue['postList']);
      ?>
      <div class="<?php echo $blockClass; ?>">
        <div class="<?php echo $blockClass; ?>__wrap">
          <ul class="<?php echo $blockClass; ?>__ul">
            <?php foreach ($blockValue as $list) : ?>
              <li class="<?php echo $blockClass; ?>__li">
                <?php
                $boxClass = 'b-box__tate';
                $boxValue = $list;
                $boxUrl = SetBoxLink($boxValue['link'])['url'];
                $boxTarget = SetBoxLink($boxValue['link'])['target'];
                ?>
                <div class="<?php echo $boxClass; ?>">
                  <div class="<?php echo $boxClass; ?>__wrap">
                    <a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
                      <?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
                    </a>
                    <div class="<?php echo $boxClass; ?>__textBox textBox">
                      <div class="info">
                        <?php setHtmlText($boxValue['date'], 'p-date'); ?>
                        <?php setHtmlTaxonomy($boxValue['taxonomy']['category'] ?? [], 'p-category', false, true); ?>
                      </div>
                      <a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
                        <?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
                        <?php setHtmlText($boxValue['text'], 'p-text'); ?>
                      </a>
                      <?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
                    </div>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
          <?php
          if ($sectionValue['postList']['pager'] ?? false) {
            setHtmlPagerNum();
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</section>
`;
}

function pageSingleTemplate(slug, name) {
  return `<?php setHtmlMv($this_page_value, 'b-mv__sub'); ?>

<?php
$sectionId = "main";
$sectionClass = "page-${slug}__" . $sectionId;
$sectionValue = $this_page_value;
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
  <div class="section__wrap">
    <div class="<?php echo $sectionClass; ?>__wrap">
      <?php setHtmlBody($sectionValue['body'] ?? []); ?>
    </div>
  </div>
</section>
`;
}

function valuePageTemplate(slug, name, nameEn) {
  const $ = "$";
  return (
    `<?php\n` +
    `${$}p_key = "${slug}";\n` +
    `${$}{'page_' . ${$}p_key} = defaultPageValue(${$}p_key, array(\n` +
    `\t'title' => setValueTitle('${name}', '${nameEn}'),\n` +
    `\t'date' => date(${$}date_format),\n` +
    `\t'class' => 'page-' . ${$}p_key,\n` +
    `\t'id' => 0,\n` +
    `\t'slug' => ${$}p_key,\n` +
    `\t'post_type' => 'page',\n` +
    `\t'section_mode' => ${$}p_key,\n` +
    `\t'nav' => setPageNav(${$}p_key),\n` +
    `\t'pankuzu' => '',\n` +
    `\t'parent' => 'home',\n` +
    `\t'taxonomy' => array(),\n` +
    `\t'image' => setValueImage(${$}image_path . '_dummy/pic-dummy.webp', '画像タイトル'),\n` +
    `\t'text' => '',\n` +
    `\t'meta' => array(\n` +
    `\t\t'ogp' => setValueImage(${$}image_path . '_dummy/pic-dummy.webp', '画像タイトル'),\n` +
    `\t\t'description' => '',\n` +
    `\t),\n` +
    `\t'thumbnail' => array(\n` +
    `\t\t'image' => setValueImage(${$}image_path . '_dummy/pic-dummy.webp', '画像タイトル'),\n` +
    `\t\t'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',\n` +
    `\t),\n` +
    `));\n` +
    `\n` +
    `${$}acfvalues[] = addAcfValueArray(${$}p_key, ${$}p_key . ' page', 'page', array(\n` +
    `\tsetAcfTitle('title', "ページタイトル", array('main', 'sub'), 'table'),\n` +
    `\tarray(\n` +
    `\t\t'type' => "group",\n` +
    `\t\t'label' => 'セクション',\n` +
    `\t\t'name' => 'section',\n` +
    `\t\t'layout' => 'block',\n` +
    `\t\t'sub_fields' => array(\n` +
    `\t\t\tarray(\n` +
    `\t\t\t\t'type' => "repeater",\n` +
    `\t\t\t\t'label' => 'リスト',\n` +
    `\t\t\t\t'name' => 'list',\n` +
    `\t\t\t\t'layout' => 'row',\n` +
    `\t\t\t\t'button_label' => 'リストを追加',\n` +
    `\t\t\t\t'sub_fields' => array(\n` +
    `\t\t\t\t\tsetAcfImage(),\n` +
    `\t\t\t\t\tsetAcfTitle('title', "見出し", array('main', 'sub'), 'table'),\n` +
    `\t\t\t\t\tsetAcfText(),\n` +
    `\t\t\t\t\tsetAcfLink(),\n` +
    `\t\t\t\t),\n` +
    `\t\t\t),\n` +
    `\t\t),\n` +
    `\t),\n` +
    `));\n`
  );
}

function valuePostTemplate(pkey, name, nameEn, taxonomy) {
  const $ = "$";
  const taxKeys = (taxonomy || []).map((t) => `\t\t'${t.slug}' => array(${$}{$p_key . '_value'}['${t.slug}'][0]),`).join("\n");

  const valueBlock =
    `${$}{$p_key . '_value'} = array();\n` +
    `foreach ($custompostarray as $cpt) {\n` +
    `\tif ($cpt['slug'] == $p_key) {\n` +
    `\t\tforeach ($cpt['taxonomy'] as $thistaxonomy) {\n` +
    `\t\t\t${$}{$p_key . '_value'}[$thistaxonomy['slug']] = setValueTaxonmy($p_key, '_' . $thistaxonomy['slug'], $thistaxonomy['value']);\n` +
    `\t\t}\n` +
    `\t}\n` +
    `}\n`;

  const taxLoopBlock =
    `foreach (${$}{$p_key . '_value'} as $taxkey => $thistax) {\n` +
    `\t${$}{$p_key . '_' . $taxkey . '_list'} = array();\n` +
    `\tforeach ($thistax as $tax) {\n` +
    `\t\t$thistaxarr = ${$}{'page_' . $p_key};\n` +
    `\t\t$thistaxarr['title'] = setValueTitle($tax->name, $p_key . ' ' . $taxkey);\n` +
    `\t\t${$}{$p_key . '_' . $taxkey . '_list'}[] = $thistaxarr;\n` +
    `\t}\n` +
    `}\n`;

  return (
    `<?php\n` +
    `$p_key = "${pkey}";\n` +
    valueBlock +
    `${$}{$p_key . '_list'} = array(\n` +
    `\tarray(\n` +
    `\t\t'title'        => setValueTitle('${name}の見出しが入ります。'),\n` +
    `\t\t'date'         => date($date_format),\n` +
    `\t\t'class'        => 'page-' . $p_key,\n` +
    `\t\t'id'           => 0,\n` +
    `\t\t'post_type'    => $p_key,\n` +
    `\t\t'slug'         => 'detail.php?id=0',\n` +
    `\t\t'section_mode' => $p_key . '_single',\n` +
    `\t\t'pankuzu'      => '',\n` +
    `\t\t'parent'       => $p_key,\n` +
    `\t\t'taxonomy'     => array(\n` +
    taxKeys +
    (taxKeys ? "\n" : "") +
    `\t\t),\n` +
    `\t\t'image'     => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),\n` +
    `\t\t'thumbnail' => array(\n` +
    `\t\t\t'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),\n` +
    `\t\t\t'text'  => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',\n` +
    `\t\t),\n` +
    `\t\t'mv' => array(\n` +
    `\t\t\t'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),\n` +
    `\t\t\t'text'  => '',\n` +
    `\t\t),\n` +
    `\t\t'body' => array(),\n` +
    `\t),\n` +
    `);\n` +
    `${$}{'page_' . $p_key} = defaultPageValue($p_key, array(\n` +
    `\t'title'        => setValueTitle('${name}', "${nameEn}"),\n` +
    `\t'section_mode' => $p_key . '_archive',\n` +
    `\t'image'        => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),\n` +
    `\t'text'         => '',\n` +
    `\t'thumbnail'    => array(\n` +
    `\t\t'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),\n` +
    `\t\t'text'  => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',\n` +
    `\t),\n` +
    `\t'mv'           => array(\n` +
    `\t\t'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),\n` +
    `\t\t'text'  => '',\n` +
    `\t),\n` +
    `\t'postList'     => array(\n` +
    `\t\t'cpt'   => $p_key,\n` +
    `\t\t'pager' => true,\n` +
    `\t\t'list'  => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0),\n` +
    `\t),\n` +
    `));\n` +
    `\n` +
    taxLoopBlock +
    `\n` +
    `$acfvalues[] = addAcfValueArray($p_key, $p_key . ' 一覧', 'archive', array(\n` +
    `\tsetAcfTitle('title', "ページタイトル", array('main', 'sub'), 'table'),\n` +
    `\tsetAcfMv(),\n` +
    `\tsetAcfPostList(),\n` +
    `));\n` +
    `\n` +
    `$acfvalues[] = addAcfValueArray($p_key, $p_key . ' 詳細', 'single', array(\n` +
    `\tsetAcfTitle('title', "ページタイトル", array('main', 'sub'), 'table'),\n` +
    `\tsetAcfMv(),\n` +
    `\tsetAcfBody2('body', $p_key, 'block'),\n` +
    `));\n`
  );
}

function valueFormTemplate(slug, name, nameEn) {
  const $ = "$";
  return (
    `<?php\n` +
    `${$}p_key = "${slug}";\n` +
    `${$}contact_input = array(\n` +
    `\tarray(\n` +
    `\t\t'type' => 'radio',\n` +
    `\t\t'name' => 'your-radio',\n` +
    `\t\t'label' => 'カテゴリ',\n` +
    `\t\t'req' => false,\n` +
    `\t\t'inputlist' => array(\n` +
    `\t\t\t"doc" => "資料請求",\n` +
    `\t\t\t"contact" => "お問い合わせ",\n` +
    `\t\t)\n` +
    `\t),\n` +
    `\tarray(\n` +
    `\t\t'type' => 'select',\n` +
    `\t\t'name' => 'your-select',\n` +
    `\t\t'label' => 'セレクトボックス',\n` +
    `\t\t'req' => false,\n` +
    `\t\t'inputlist' => array(\n` +
    `\t\t\t"doc" => "資料請求",\n` +
    `\t\t\t"contact" => "お問い合わせ",\n` +
    `\t\t)\n` +
    `\t),\n` +
    `\tarray(\n` +
    `\t\t'type' => 'text',\n` +
    `\t\t'name' => 'your-name',\n` +
    `\t\t'label' => 'お名前',\n` +
    `\t\t'req' => true,\n` +
    `\t\t'placeholder' => '例）山田 太郎',\n` +
    `\t\t'error' => '必須項目です',\n` +
    `\t),\n` +
    `\tarray(\n` +
    `\t\t'type' => 'text',\n` +
    `\t\t'name' => 'your-company',\n` +
    `\t\t'label' => '会社名',\n` +
    `\t\t'req' => false,\n` +
    `\t\t'placeholder' => '例）株式会社client',\n` +
    `\t),\n` +
    `\tarray(\n` +
    `\t\t'type' => 'email',\n` +
    `\t\t'name' => 'your-email',\n` +
    `\t\t'label' => 'メールアドレス',\n` +
    `\t\t'req' => true,\n` +
    `\t\t'placeholder' => '例）info@client.com',\n` +
    `\t\t'error' => '必須項目です',\n` +
    `\t),\n` +
    `\tarray(\n` +
    `\t\t'type' => 'text',\n` +
    `\t\t'name' => 'your-tel',\n` +
    `\t\t'label' => '電話番号',\n` +
    `\t\t'req' => false,\n` +
    `\t\t'placeholder' => '例）0364413614',\n` +
    `\t),\n` +
    `\tarray(\n` +
    `\t\t'type' => 'zip',\n` +
    `\t\t'name' => 'your-zip',\n` +
    `\t\t'label' => '郵便番号',\n` +
    `\t\t'req' => false,\n` +
    `\t\t'placeholder' => '例)100-0001',\n` +
    `\t),\n` +
    `\tarray(\n` +
    `\t\t'type' => 'add',\n` +
    `\t\t'name' => 'your-add',\n` +
    `\t\t'label' => '住所',\n` +
    `\t\t'req' => false,\n` +
    `\t\t'placeholder' => '住所の続きを入力してください',\n` +
    `\t\t"class" => "w12",\n` +
    `\t),\n` +
    `\tarray(\n` +
    `\t\t'type' => 'textarea',\n` +
    `\t\t'name' => 'your-message',\n` +
    `\t\t'label' => 'お問合せ内容',\n` +
    `\t\t'req' => true,\n` +
    `\t\t"class" => "vat",\n` +
    `\t\t'cap' => array('ご要望、その他お問い合せなど自由にご記入ください。'),\n` +
    `\t),\n` +
    `);\n` +
    `\n` +
    `${$}form_setting = array(\n` +
    `\t'input' => ${$}contact_input,\n` +
    `\t'userMail' => array(\n` +
    `\t\t'to' => ${$}email,\n` +
    `\t\t'from' => ${$}email,\n` +
    `\t\t'fromName' => ${$}client_name,\n` +
    `\t\t'fromadd' => 0,\n` +
    `\t\t'subject' => '[' . ${$}client_name . ']お問い合わせありがとうございました',\n` +
    `\t\t'name' => 'your-name',\n` +
    `\t\t'email' => 'your-email',\n` +
    `\t),\n` +
    `\t'adminMail' => array(\n` +
    `\t\t'to' => ${$}email,\n` +
    `\t\t'from' => ${$}email,\n` +
    `\t\t'bcc' => '',\n` +
    `\t\t'fromadd' => 0,\n` +
    `\t\t'subject' => 'ホームページのお問い合わせがありました',\n` +
    `\t),\n` +
    `\t'confirm' => ${$}link_path . '/' . ${$}p_key . '/confirm/',\n` +
    `\t'thanks' => ${$}link_path . '/' . ${$}p_key . '/thanks/',\n` +
    `);\n` +
    `\n` +
    `${$}{'page_' . ${$}p_key} = defaultPageValue(${$}p_key, array(\n` +
    `\t'title' => setValueTitle('${name}', "${nameEn}"),\n` +
    `\t'image' => setValueImage(${$}image_path . '_dummy/pic-dummy.webp', '画像タイトル'),\n` +
    `\t'text' => '',\n` +
    `\t'setting' => ${$}form_setting,\n` +
    `\t'mailSetting' => array(\n` +
    `\t\t'adminTo' => '',\n` +
    `\t\t'adminBcc' => '',\n` +
    `\t\t'adminSubject' => '',\n` +
    `\t\t'userSubject' => '',\n` +
    `\t),\n` +
    `\t'form' => array(\n` +
    `\t\t'type' => 'input',\n` +
    `\t\t'lead' => '当社へのお問い合わせ・ご相談は、下記フォームより承っております。',\n` +
    `\t),\n` +
    `));\n` +
    `\n` +
    `${$}{'page_' . ${$}p_key . '_confirm'} = ${$}{'page_' . ${$}p_key};\n` +
    `${$}{'page_' . ${$}p_key . '_confirm'}['section_mode'] = ${$}p_key . '_confirm';\n` +
    `${$}{'page_' . ${$}p_key . '_confirm'}["form"] = array(\n` +
    `\t'type' => 'confirm',\n` +
    `\t'lead' => '内容をご確認の上送信してください。',\n` +
    `);\n` +
    `\n` +
    `${$}{'page_' . ${$}p_key . '_thanks'} = ${$}{'page_' . ${$}p_key};\n` +
    `${$}{'page_' . ${$}p_key . '_thanks'}['section_mode'] = ${$}p_key . '_thanks';\n` +
    `${$}{'page_' . ${$}p_key . '_thanks'}["form"] = array(\n` +
    `\t'type' => 'thanks',\n` +
    `\t'lead' => 'このたびは、お問合せいただき、誠にありがとうございました。',\n` +
    `\t'text' => 'お送りいただきました内容を確認の上、担当者より折り返しご連絡させていただきます。',\n` +
    `\t'link' => array(\n` +
    `\t\t'link' => array(\n` +
    `\t\t\t'url' => ${$}link_path . "/",\n` +
    `\t\t\t'title' => "TOPに戻る",\n` +
    `\t\t\t'target' => "_self",\n` +
    `\t\t),\n` +
    `\t),\n` +
    `);\n` +
    `\n` +
    `${$}acfvalues[] = addAcfValueArray(${$}p_key, ${$}p_key . ' フォーム', 'page', array(\n` +
    `\tsetAcfTitle('title', "ページタイトル", array('main', 'sub'), 'table'),\n` +
    `\tarray(\n` +
    `\t\t'type' => 'group',\n` +
    `\t\t'label' => 'メール設定（上書き用）',\n` +
    `\t\t'name' => 'mailSetting',\n` +
    `\t\t'layout' => 'block',\n` +
    `\t\t'instructions' => '空欄の場合はデフォルト値が使われます',\n` +
    `\t\t'sub_fields' => array(\n` +
    `\t\t\tarray(\n` +
    `\t\t\t\t'type' => 'email',\n` +
    `\t\t\t\t'label' => '管理者送信先メールアドレス',\n` +
    `\t\t\t\t'name' => 'adminTo',\n` +
    `\t\t\t\t'instructions' => '複数指定する場合はカンマ区切り',\n` +
    `\t\t\t),\n` +
    `\t\t\tarray(\n` +
    `\t\t\t\t'type' => 'text',\n` +
    `\t\t\t\t'label' => 'BCC',\n` +
    `\t\t\t\t'name' => 'adminBcc',\n` +
    `\t\t\t),\n` +
    `\t\t\tarray(\n` +
    `\t\t\t\t'type' => 'text',\n` +
    `\t\t\t\t'label' => '管理者通知メール件名',\n` +
    `\t\t\t\t'name' => 'adminSubject',\n` +
    `\t\t\t),\n` +
    `\t\t\tarray(\n` +
    `\t\t\t\t'type' => 'text',\n` +
    `\t\t\t\t'label' => 'ユーザー自動返信メール件名',\n` +
    `\t\t\t\t'name' => 'userSubject',\n` +
    `\t\t\t),\n` +
    `\t\t),\n` +
    `\t),\n` +
    `\tarray(\n` +
    `\t\t'type' => "group",\n` +
    `\t\t'label' => 'フォーム',\n` +
    `\t\t'name' => 'form',\n` +
    `\t\t'layout' => 'rows',\n` +
    `\t\t'sub_fields' => array(\n` +
    `\t\t\tarray(\n` +
    `\t\t\t\t'type' => 'button_group',\n` +
    `\t\t\t\t'label' => 'フォームタイプ',\n` +
    `\t\t\t\t'name' => 'type',\n` +
    `\t\t\t\t'choices' => array(\n` +
    `\t\t\t\t\t'input' => '入力画面',\n` +
    `\t\t\t\t\t'confirm' => '確認画面',\n` +
    `\t\t\t\t\t'thanks' => '完了画面',\n` +
    `\t\t\t\t),\n` +
    `\t\t\t),\n` +
    `\t\t\tsetAcfText('lead', 'リード文章', 2),\n` +
    `\t\t\tsetAcfText(),\n` +
    `\t\t\tsetAcfLink('link', 'リンク', 'table', array(\n` +
    `\t\t\t\t'field' => 'field_' . ${$}p_key . '_form_type',\n` +
    `\t\t\t\t'operator' => '==',\n` +
    `\t\t\t\t'value' => 'thanks',\n` +
    `\t\t\t)),\n` +
    `\t\t),\n` +
    `\t),\n` +
    `));\n`
  );
}

function pageFormTemplate(slug) {
  return `<?php\ninclude("${slug}_common.php");\n`;
}

function pageFormConfirmTemplate(slug) {
  return `<?php\nif ($confirmDisplay) {\n\tinclude("${slug}_common.php");\n}\n`;
}

function pageFormThanksTemplate(slug) {
  return `<?php\ninclude("${slug}_common.php");\n`;
}

function pageFormCommonTemplate(slug, name) {
  return `<?php setHtmlMv($this_page_value, 'b-mv__noimage is-center'); ?>
<?php
$sectionId = "form";
$sectionClass = $this_page_value["class"] . "__" . $sectionId;
$sectionValue = $this_page_value;
$formtype = $sectionValue['form']['type'];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
<div class="section__wrap">
\t<div class="<?php echo $sectionClass; ?>__wrap">
\t\t<div class="<?php echo $sectionClass; ?>__inner">
\t\t\t<div class="<?php echo $sectionClass ?>__textBox">
\t\t\t\t<?php
\t\t\t\tif (!empty($sectionValue['form']["lead"])) {
\t\t\t\t\tsetHtmlText($sectionValue['form']["lead"], 'p-text');
\t\t\t\t}
\t\t\t\tif (!empty($sectionValue['form']["text"])) {
\t\t\t\t\tsetHtmlText($sectionValue['form']["text"], 'p-text');
\t\t\t\t}
\t\t\t\tif (!empty($sectionValue['form']["link"])) : ?>
\t\t\t\t\t<div class="b-links">
\t\t\t\t\t\t<div class="b-links__wrap">
\t\t\t\t\t\t\t<?php setHtmlLink($sectionValue['form']["link"], 'p-button'); ?>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t<?php endif; ?>
\t\t\t</div>
\t\t\t<?php
\t\t\tif ($formtype != "thanks") {
\t\t\t\tsetHtmlForm($sectionValue, $formtype, $sectionClass);
\t\t\t\tif ($formtype == "confirm" && !empty($errm)) {
\t\t\t\t\techo $errm;
\t\t\t\t}
\t\t\t}
\t\t\t?>
\t\t</div>
\t\t</div>
\t</div>
</section>
`;
}

function scssTemplate(slug) {
  return `@use "../../global" as global;@use "sass:math";

.page-${slug} {
  // styles here
}
`;
}

// ─── ファイル書き込み ─────────────────────────────────────────

function writeFileIfNotExists(filePath, content, description) {
  if (fs.existsSync(filePath)) {
    console.log(`  SKIP (exists)  : ${description}`);
    return;
  }
  fs.mkdirSync(path.dirname(filePath), { recursive: true });
  fs.writeFileSync(filePath, content, "utf8");
  console.log(`  CREATE         : ${description} → ${path.relative(ROOT_DIR, filePath)}`);
}

function addScssForward(slug) {
  if (!fs.existsSync(SCSS_INDEX)) return;

  const content = fs.readFileSync(SCSS_INDEX, "utf8");
  const forward = `@forward "${slug}";`;

  if (content.includes(forward)) {
    console.log(`  SKIP FORWARD   : ${forward}`);
    return;
  }

  const newContent = content.replace(/@forward "styleguide";/, `@forward "${slug}";\n@forward "styleguide";`);

  if (newContent !== content) {
    fs.writeFileSync(SCSS_INDEX, newContent, "utf8");
  } else {
    fs.appendFileSync(SCSS_INDEX, `${forward}\n`);
  }
  console.log(`  ADD FORWARD    : ${forward}`);
}

function addJaInclude(slug) {
  if (!fs.existsSync(JA_PHP)) return;

  const content = fs.readFileSync(JA_PHP, "utf8");
  const includeLine = `include('page/${slug}.php');`;

  if (content.includes(includeLine)) {
    console.log(`  SKIP INCLUDE   : ${includeLine}`);
    return;
  }

  // 最後の include('page/...'); を見つけてその後に挿入
  const lastIncludeMatch = content.match(/^(include\('page\/[^']+'\);)\s*$/gm);
  if (lastIncludeMatch) {
    const lastInclude = lastIncludeMatch[lastIncludeMatch.length - 1];
    const newContent = content.replace(lastInclude, lastInclude + "\n" + includeLine);
    fs.writeFileSync(JA_PHP, newContent, "utf8");
  } else {
    // include が1つもない場合はファイル末尾に追加
    fs.appendFileSync(JA_PHP, "\n" + includeLine + "\n");
  }
  console.log(`  ADD INCLUDE    : ${includeLine}`);
}

// ─── エントリ処理 ─────────────────────────────────────────────

/**
 * @param {object} entry       - sitemapの各エントリ
 * @param {string[]} htmlPath  - 現在のhtmlディレクトリパス（祖先slugの配列）
 * @param {string|null} parentPostType - 親のpost_type
 */
function processEntry(entry, htmlPath, parentPostType) {
  const { slug, name = slug, name_en: nameEn = "", post_type, children, taxonomy } = entry;

  // ── スキップ判定 ──
  if (SKIP_SLUGS.includes(slug)) {
    console.log(`  SKIP (slug)    : ${slug}`);
    return;
  }

  if (SKIP_POST_TYPES.includes(post_type)) {
    console.log(`  SKIP (type=${post_type}) : ${slug}`);
    return;
  }

  const currentHtmlPath = [...htmlPath, slug];
  const depth = currentHtmlPath.length;
  const htmlDir = path.join(HTML_DIR, ...currentHtmlPath);
  const isTopLevel = htmlPath.length === 0;
  const isChildOfPost = parentPostType === "post";

  // compound slug: company/message → company_message
  const compoundSlug = currentHtmlPath.join("_");

  console.log(`\n[${post_type}] ${currentHtmlPath.join("/")}`);

  // ── page タイプ ──
  if (post_type === "page") {
    writeFileIfNotExists(path.join(htmlDir, "index.php"), htmlIndexTemplate(depth), `html/index (${compoundSlug})`);

    writeFileIfNotExists(path.join(PAGE_DIR, `${compoundSlug}.php`), pageTemplate(compoundSlug, name), `page/${compoundSlug}.php`);

    // 親が post タイプの場合 value/scss は生成しない
    if (!isChildOfPost) {
      writeFileIfNotExists(path.join(VALUE_DIR, `${compoundSlug}.php`), valuePageTemplate(compoundSlug, name, nameEn), `value/page/${compoundSlug}.php`);
      addJaInclude(compoundSlug);
    }

    // scss はトップレベルのみ
    if (isTopLevel) {
      writeFileIfNotExists(path.join(SCSS_DIR, `_${slug}.scss`), scssTemplate(slug), `scss/_${slug}.scss`);
      addScssForward(slug);
    }
  }

  // ── post タイプ ──
  else if (post_type === "post") {
    writeFileIfNotExists(path.join(htmlDir, "index.php"), htmlIndexTemplate(depth), `html/index (${slug})`);

    writeFileIfNotExists(path.join(htmlDir, "detail.php"), htmlDetailTemplate(depth), `html/detail (${slug})`);

    writeFileIfNotExists(path.join(PAGE_DIR, `${slug}_archive.php`), pageArchiveTemplate(slug, name), `page/${slug}_archive.php`);

    writeFileIfNotExists(path.join(PAGE_DIR, `${slug}_single.php`), pageSingleTemplate(slug, name), `page/${slug}_single.php`);

    writeFileIfNotExists(path.join(VALUE_DIR, `${slug}.php`), valuePostTemplate(slug, name, nameEn, taxonomy), `value/page/${slug}.php`);
    addJaInclude(slug);

    if (isTopLevel) {
      writeFileIfNotExists(path.join(SCSS_DIR, `_${slug}.scss`), scssTemplate(slug), `scss/_${slug}.scss`);
      addScssForward(slug);
    }
  }

  // ── form タイプ ──
  else if (post_type === "form") {
    writeFileIfNotExists(path.join(htmlDir, "index.php"), htmlIndexTemplate(depth), `html/index (${slug})`);

    writeFileIfNotExists(path.join(htmlDir, "confirm", "index.php"), htmlIndexTemplate(depth + 1), `html/confirm/index (${slug})`);

    writeFileIfNotExists(path.join(htmlDir, "thanks", "index.php"), htmlIndexTemplate(depth + 1), `html/thanks/index (${slug})`);

    writeFileIfNotExists(path.join(PAGE_DIR, `${slug}.php`), pageFormTemplate(slug), `page/${slug}.php`);

    writeFileIfNotExists(path.join(PAGE_DIR, `${slug}_confirm.php`), pageFormConfirmTemplate(slug), `page/${slug}_confirm.php`);

    writeFileIfNotExists(path.join(PAGE_DIR, `${slug}_thanks.php`), pageFormThanksTemplate(slug), `page/${slug}_thanks.php`);

    writeFileIfNotExists(path.join(PAGE_DIR, `${slug}_common.php`), pageFormCommonTemplate(slug, name), `page/${slug}_common.php`);

    writeFileIfNotExists(path.join(VALUE_DIR, `${slug}.php`), valueFormTemplate(slug, name, nameEn), `value/page/${slug}.php`);
    addJaInclude(slug);

    if (isTopLevel) {
      writeFileIfNotExists(path.join(SCSS_DIR, `_${slug}.scss`), scssTemplate(slug), `scss/_${slug}.scss`);
      addScssForward(slug);
    }
  }

  // ── children の処理 ──
  if (Array.isArray(children) && children.length > 0) {
    for (const child of children) {
      processEntry(child, currentHtmlPath, post_type);
    }
  } else if (children !== undefined && children !== null && !Array.isArray(children) && typeof children === "object") {
    // { type: "taxonomy" | "post" } 形式 — ナビ自動生成用、ページ生成不要
    console.log(`  SKIP (children object, nav-only): ${slug}.children`);
  }
}

// ─── メイン ──────────────────────────────────────────────────

const sitemap = JSON.parse(fs.readFileSync(SITEMAP_PATH, "utf8"));

console.log("=== generate-pages start ===");
for (const entry of sitemap) {
  processEntry(entry, [], null);
}
console.log("\n=== generate-pages done ===");
