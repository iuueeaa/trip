<?php
$p_key = "contact";
$form_setting = array(
	'input' => array(
		array(
			'type' => 'radio',
			'name' => 'your-radio',
			'label' =>  'カテゴリ',
			'req' => false,
			'inputlist' => array(
				"doc" => "資料請求",
				"contact" => "お問い合わせ",
			)
		),
		array(
			'type' => 'checkbox',
			'name' => 'your-checkbox',
			'label' =>  'カテゴリ',
			'req' => false,
			'inputlist' => array(
				"doc" => "資料請求",
				"contact" => "お問い合わせ",
			)
		),
		array(
			'type' => 'select',
			'name' => 'your-select',
			'label' =>  'セレクトボックス',
			'req' => false,
			'inputlist' => array(
				"doc" => "資料請求",
				"contact" => "お問い合わせ",
			)
		),
		array(
			'type' => 'text',
			'name' => 'your-name',
			'label' =>  'お名前',
			'req' => true,
			'placeholder' => '例）山田 太郎',
			'error' => '必須項目です',
		),
		array(
			'type' => 'text',
			'name' => 'your-company',
			'label' =>  '会社名',
			'req' => false,
			'placeholder' => '例）株式会社client',
		),
		array(
			'type' => 'email',
			'name' => 'your-email',
			'label' =>  'メールアドレス',
			'req' => true,
			'placeholder' => '例）info@client.com',
			'error' => '必須項目です',
		),
		array(
			'type' => 'text',
			'name' => 'your-tel',
			'label' =>  '電話番号',
			'req' => false,
			'placeholder' => '例）0364413614',
		),
		array(
			'type' => 'zip',
			'name' => 'your-zip',
			'label' =>  '郵便番号',
			'req' => false,
			'placeholder' => '例)100-0001',
		),
		array(
			'type' => 'add',
			'name' => 'your-add',
			'label' =>  '住所',
			'req' => false,
			'placeholder' => '住所の続きを入力してください',
			"class" => "w12",
		),
		array(
			'type' => 'textarea',
			'name' => 'your-message',
			'label' =>  'お問合せ内容',
			'req' => true,
			"class" => "vat",
			'cap' => array('ご要望、その他お問い合せなど自由にご記入ください。'),
		),
	),
	'userMail' => array(
		'to' => $email,
		'from' => $email,
		'fromName' => $client_name,
		'fromadd' => 0,
		'subject' => '[' . $client_name . ']お問い合わせありがとうございました',
		'name' => 'your-name',
		'email' => 'your-email',
	),
	'adminMail' => array(
		'to' => $email, //(複数指定する場合は「,」で区切り
		'from' => $email,
		'bcc' => '',
		'fromadd' => 0,
		'subject' => 'ホームページのお問い合わせがありました',
	),
	'confirm' => $link_path . '/contact/confirm/',
	'thanks' => $link_path . '/contact/thanks/'
);


${'page_' . $p_key}  = defaultPageValue(
	$p_key,
	array(
		'title' => setValueTitle('お問い合わせ', "Contact"), //mainが日, subが英
		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'text' => '',
		'setting' => $form_setting,
		'mailSetting' => array(
			'adminTo' => '',
			'adminBcc' => '',
			'adminSubject' => '',
			'userSubject' => '',
		),
		'form' => array(
			'type' => 'input',
			'lead' => '当社へのお問い合わせ・ご相談または、当社サービスへのお問い合わせは、下記フォームより承っております。',
		),
	)
);


${'page_' . $p_key . '_confirm'} = ${'page_' . $p_key};
${'page_' . $p_key . '_confirm'}['section_mode'] = $p_key . '_confirm';
${'page_' . $p_key . '_confirm'}["form"] = array(
	'type' => 'confirm',
	'lead' => '内容をご確認の上送信してください。',
);

${'page_' . $p_key . '_thanks'} = ${'page_' . $p_key};
${'page_' . $p_key . '_thanks'}['section_mode'] = $p_key . '_thanks';
${'page_' . $p_key . '_thanks'}["form"] = array(
	'type' => 'thanks',
	'lead' => 'このたびは、お問合せいただき、<br class="show_sp">誠にありがとうございました。',
	'text' => 'お送りいただきました内容を確認の上、担当者より折り返しご連絡させていただきます。<br>また、ご記入いただきましたメールアドレスへ、自動返信の確認メールを送付しています。<br>自動返信メールが届かない場合、入力いただいたメールアドレスに誤りがあった可能性がございます。<br>メールアドレスをご確認の上、もう一度フォームよりお問合せ頂きますようお願い申し上げます。',
	'link' => array(
		'link' => array(
			'url' => $link_path . "/",
			'title' => "TOPに戻る",
			'target' =>  "_self",
		),
	)

);


$acfvalues[] =	addAcfValueArray($p_key, 'お問い合わせ・フォーム', 'page', array(
	setAcfTitle('title', 'ページタイトル', array('main', 'sub'), 'table'),
	// setAcfMvMin(),
	array(
		'type' => 'group',
		'label' => 'メール設定（上書き用）',
		'name' => 'mailSetting',
		'layout' => 'block',
		'instructions' => '空欄の場合はデフォルト値が使われます',
		'sub_fields' => array(
			array(
				'type' => 'email',
				'label' => '管理者送信先メールアドレス',
				'name' => 'adminTo',
				'instructions' => '複数指定する場合はカンマ区切り',
			),
			array(
				'type' => 'text',
				'label' => 'BCC',
				'name' => 'adminBcc',
			),
			array(
				'type' => 'text',
				'label' => '管理者通知メール件名',
				'name' => 'adminSubject',
			),
			array(
				'type' => 'text',
				'label' => 'ユーザー自動返信メール件名',
				'name' => 'userSubject',
			),
		),
	),
	array(
		'type' => "group",
		'label' => 'フォーム',
		'name' => 'form',
		'layout' => 'rows',
		'sub_fields' =>  array(
			array(
				'type' => 'button_group',
				'label' => 'フォームタイプ',
				'name' => 'type',
				'choices' => array(
					'input' => '入力画面',
					'confirm' => '確認画面',
					'thanks' => '完了画面',
				),
			),
			// array(
			// 	'type' => 'text',
			// 	'label' => 'フォームID',
			// 	'name' => 'formid',
			// ),
			// setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
			setAcfText('lead', 'リード文章', 2),
			setAcfText(),
			setAcfLink('link', 'リンク', 'table',	array(
				'field' => 'field_contact_form_type',
				'operator' => '==',
				'value' => 'thanks',
			),),
		),
	),
));
