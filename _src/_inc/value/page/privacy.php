<?php
$p_key = "privacy";
${'page_' . $p_key} = defaultPageValue(
	$p_key,
	array(
		'title' => setValueTitle('個人情報保護方針', "Privacy Policy"), //mainが日, subが英
		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		// 'meta' => array(
		// 	'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		// 	'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		// ),
		// 'thumbnail' => array(
		// 	'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		// 	'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		// ),
		'body' => array(
			array(
				'acf_fc_layout' => 'text',
				'text' => '（以下「当社」）は、当社が取得した利用者の個人情報の取扱いについて、下記のとおり個人情報保護方針を定め、個人情報の保護に関する法律（以下｢個人情報保護法｣といいます。）、個人情報保護に関するガイドライン等の指針、その他の関係法令とともに、これを遵守します。',
			),
			array(
				'acf_fc_layout' => 'title',
				'title' => array(
					'h' => 'h2',
					'main' => '個人情報の定義',
					'sub' => '',
				),
			),
			array(
				'acf_fc_layout' => 'text',
				'text' => '「個人情報」とは、個人情報保護法にいう「個人情報」を指すものとし、生存する個人に関する情報であって、当該情報に含まれる氏名、生年月日、住所、電話番号、連絡先その他の記述等により特定の個人を識別できる情報及び容貌、指紋、声紋にかかるデータ、及び健康保険証の保険者番号などの当該情報単体から特定の個人を識別できる情報（個人識別情報）を指します。',
			),
			array(
				'acf_fc_layout' => 'title',
				'title' => array(
					'h' => 'h2',
					'main' => '個人情報の利用目的',
					'sub' => '',
				),
			),
			array(
				'acf_fc_layout' => 'text',
				'text' => '当社は、下記の目的のために必要な範囲内において、個人情報を利用いたします。',
			),
			array(
				'acf_fc_layout' => 'title',
				'title' => array(
					'h' => 'h3',
					'main' => '当社での利用',
					'sub' => '',
				),
			),
			array(
				'acf_fc_layout' => 'li',
				'li' => array(
					'type' => 'ul',
					'li' => array(
						array(
							'text' => '本サイトの運営、維持、管理',
						),
						array(
							'text' => '本サイトを通じたサービスの提供及び紹介',
						),
						array(
							'text' => '本サイトの品質向上のためのアンケート',
						),
					),
				),
			),
			array(
				'acf_fc_layout' => 'title',
				'title' => array(
					'h' => 'h3',
					'main' => '第三者への提供(当社で利用される場合も含む)',
					'sub' => '',
				),
			),
			array(
				'acf_fc_layout' => 'li',
				'li' => array(
					'type' => 'ul',
					'li' => array(
						array(
							'text' => '紛争や訴訟等へ対応する場合における、関係者や関係機関への情報の提出のため',
						),
						array(
							'text' => '関係法令等に基づく行政機関及び司法機関への情報の提出のため',
						),
					),
				),
			),
			array(
				'acf_fc_layout' => 'title',
				'title' => array(
					'h' => 'h2',
					'main' => '委託先の管理',
					'sub' => '',
				),
			),
			array(
				'acf_fc_layout' => 'text',
				'text' => '当社は、利用目的の実施に必要な範囲で、個人情報の取り扱いを外部に委託することがありますが、委託する場合には当社が個人情報を適切に取り扱うと認める委託先を選定します。また、当社は、委託先に対し、利用目的の実施に必要な範囲に限定して個人情報を提供し、契約等により個人情報の適切な取り扱いを求め、その状況について定期的に確認します。',
			),
			array(
				'acf_fc_layout' => 'title',
				'title' => array(
					'h' => 'h2',
					'main' => '匿名情報の取扱',
					'sub' => '',
				),
			),
			array(
				'acf_fc_layout' => 'text',
				'text' => '当社の本サービスを利用した際に、利用者に関する匿名情報(IPアドレス、機能の利用状況、利用時間等の情報)がWebサーバに自動的に記録されます。この情報は以下の目的に利用されます。',
			),
			array(
				'acf_fc_layout' => 'li',
				'li' => array(
					'type' => 'ul',
					'li' => array(
						array(
							'text' => 'サーバで発生した問題の原因を解明し、それを解決するため',
						),
						array(
							'text' => '不正アクセスの有無を監視するため',
						),
						array(
							'text' => '本サービスの改善及び開発のため',
						),
					),
				),
			),
			array(
				'acf_fc_layout' => 'text',
				'text' => '利用者が当社に個人を特定できるような情報を提供しない限り、当社が匿名情報のみを使用して利用者個人を特定することはできません。',
			),
			array(
				'acf_fc_layout' => 'title',
				'title' => array(
					'h' => 'h2',
					'main' => '個人情報の第三者への提供',
					'sub' => '',
				),
			),
			array(
				'acf_fc_layout' => 'text',
				'text' => '当社は、「2 個人情報の利用目的」で規定された範囲内で第三者への提供を行うことがありますが、その他の場合は、次に掲げる場合を除き、利用者の事前の同意を得ないで、個人情報を第三者に提供しません。',
			),
			array(
				'acf_fc_layout' => 'li',
				'li' => array(
					'type' => 'ul',
					'li' => array(
						array(
							'text' => '法令に基づく場合',
						),
						array(
							'text' => '人の生命、身体または財産の保護のために必要がある場合であって、利用者の同意を得ることが困難である場合',
						),
						array(
							'text' => '公衆衛生の向上または児童の健全な育成の推進のために特に必要がある場合であって、利用者の同意を得ることが困難である場合',
						),
						array(
							'text' => '国の機関もしくは地方公共団体またはその委託を受けた者が法令の定める事務を遂行することに対して協力する必要がある場合であって、利用者の同意を得ることによってその事務の遂行に支障を及ぼすおそれがあると当社が判断した場合',
						),
						array(
							'text' => '当社が利用目的の達成に必要な範囲内において個人情報の取扱いの全部または一部を委託する場合',
						),
						array(
							'text' => '裁判所、検察庁、警察、弁護士会またはこれらに準じた権限を有する機関から、利用者の個人情報についての開示を求められた場合',
						),
					),
				),
			),
			array(
				'acf_fc_layout' => 'title',
				'title' => array(
					'h' => 'h2',
					'main' => '個人情報の変更・廃棄',
					'sub' => '',
				),
			),
			array(
				'acf_fc_layout' => 'text',
				'text' => '当社は、本人の求めによる個人情報の開示、訂正、追加若しくは削除又は利用目的の通知については、法令に従いこれを行うとともに、ご意見、ご相談に関して適切に対応します。また、個人情報の利用目的に照らしその必要性が失われたときは、個人情報を消去又は廃棄するものとし、当該消去及び廃棄は、外部流失等の危険を防止するために必要かつ適切な方法により、業務の遂行上必要な限りにおいて行います。',
			),
			array(
				'acf_fc_layout' => 'title',
				'title' => array(
					'h' => 'h2',
					'main' => '個人情報保護方針の変更要求',
					'sub' => '',
				),
			),
			array(
				'acf_fc_layout' => 'text',
				'text' => '当社は、利用者の個人情報の取扱いに関する運用状況を適宜見直し、継続的な改善に努めるものとし、その必要に応じて、本個人情報保護方針を変更することがあります。変更した場合は、当ウェブサイトに掲載いたします。',
			),
			array(
				'acf_fc_layout' => 'title',
				'title' => array(
					'h' => 'h2',
					'main' => '個人情報の取扱いに関するご意見、お問い合わせ、苦情等の窓口',
					'sub' => '',
				),
			),
			array(
				'acf_fc_layout' => 'text',
				'text' => '当社の個人情報の取扱いに関するご意見、お問い合わせ、苦情等につきましては、下記の窓口までご連絡ください。直接ご来社いただいてのお申し出は受けかねますので、その旨ご了承賜りますようお願い申し上げます。',
			),
			array(
				'acf_fc_layout' => 'ppcontact',
				'ppcontact' => array(
					'title' => 'お問い合わせ先',
					// 'text' => '株式会社XXXXXXXXXX<br />個人情報保護窓口担当<br /><a class="p-link" href="mailto:info@xxxxxxxx.com"><span>info@xxxxxxxx.com</span></a>',
					'text' => $client_name . '<br />個人情報保護窓口担当<br /><a class="p-link" href="mailto:' . $email . '"><span>' . $email . '</span></a>',
				),
			),
			array(
				'acf_fc_layout' => 'sign',
				'sign' => array(
					'date' => '制定日 : 2017.05.01／更新日 : 2023.06.04',
					'title' => '代表取締役',
					'people' => '山田太郎',
				),
			),
		)
	)
);


registerAcfFromValue($p_key, '個人情報保護方針・利用規約', 'page', ${'page_' . $p_key});
