<?php
header("Content-Type:text/html;charset=utf-8");
if (version_compare(PHP_VERSION, '5.1.0', '>=')) { //PHP5.1.0以上の場合のみタイムゾーンを定義
	date_default_timezone_set('Asia/Tokyo'); //タイムゾーンの設定（日本以外の場合には適宜設定ください）
}

// 自動返信メール
$remail_text = <<< TEXT
この度は、$client_name お問い合わせいただき誠にありがとうございます。
下記の内容でお問合せを承りました。
3営業日以内に担当者より追ってご連絡させていただきますので、
少々お待ちくださいませ。

TEXT;


$mailSignature = <<< FOOTER
===========================
$client_name
$zip $add
$buil
TEL：$tel
E-mail：$email
URL： $site_url
===========================
FOOTER;


if (!empty($this_page_value['setting'])) {
	$form_setting = $this_page_value['setting'];
	// WP環境: ACFのmailSetting値で$form_settingを上書き
	if (!empty($this_page_value['mailSetting'])) {
		$ms = $this_page_value['mailSetting'];
		if (!empty($ms['adminTo']))      $form_setting['adminMail']['to'] = $ms['adminTo'];
		if (!empty($ms['adminBcc']))     $form_setting['adminMail']['bcc'] = $ms['adminBcc'];
		if (!empty($ms['adminSubject'])) $form_setting['adminMail']['subject'] = $ms['adminSubject'];
		if (!empty($ms['userSubject']))  $form_setting['userMail']['subject'] = $ms['userSubject'];
	}
	$Email = $form_setting['userMail']['email'];
	//(する=1, しない=0)
	$confirmDsp = 1; // 送信確認画面の表示
	$jumpPage = 1; // 送信完了後に自動的に指定のページ(サンクスページなど)に移動
	$remail = 1; // 差出人に送信内容確認メール（自動返信メール）を送る
	$useToken = 1; //セッションによるワンタイムトークン（CSRF対策、及びスパム防止）
	$mail_check = 1; //メールアドレスの形式チェックを行うかどうか。
	$mailFooterDsp = 1; //自動返信メールに署名（フッター）を表示(する=1, しない=0)※管理者宛にも表示されます。


	// 下記未設定
	$Referer_check = 0; //スパム防止のためのリファラチェック（フォーム側とこのファイルが同一ドメインであるかどうかのチェック）
	$Referer_check_domain = $_SERVER["HTTP_HOST"];
	$hankaku = 0; //全角英数字→半角変換を行うかどうか。(する=1, しない=0)
	$hankaku_array = array('電話番号', '金額'); //全角英数字→半角変換を行う項目のname属性の値（name="○○"の「○○」部分）
	$use_envelope = 1; //-fオプションによるエンベロープFrom（Return-Path）の設定(する=1, しない=0)　
	//※宛先不明（間違いなどで存在しないアドレス）の場合に 管理者宛に「Mail Delivery System」から「Undelivered Mail Returned to Sender」というメールが届きます。
	//サーバーによっては稀にこの設定が必須の場合もあります。
	//設置サーバーでPHPがセーフモードで動作している場合は使用できませんので送信時にエラーが出たりメールが届かない場合は「0」（OFF）として下さい。


	$require = array();
	foreach ($form_setting['input'] as $key => $value) {
		if ($value['req']) {
			$require[] = $value['name'];
		}
	}



	//----------------------------------------------------------------------
	//  関数実行、変数初期化
	//----------------------------------------------------------------------
	if ($useToken == 1 && $confirmDsp == 1) {
		session_name('PHPMAILFORMSYSTEM');
		session_start();
		// var_dump('Session Start');
	}
	// var_dump($_SESSION['mailform_token']);
	// var_dump($_POST['mailform_token']);

	$encode = "UTF-8"; //このファイルの文字コード定義（変更不可）
	if (isset($_GET)) $_GET = sanitize($_GET); //NULLバイト除去//
	if (isset($_POST)) $_POST = sanitize($_POST); //NULLバイト除去//
	if (isset($_COOKIE)) $_COOKIE = sanitize($_COOKIE); //NULLバイト除去//
	if ($encode == 'SJIS') $_POST = sjisReplace($_POST, $encode); //Shift-JISの場合に誤変換文字の置換実行
	$funcRefererCheck = refererCheck($Referer_check, $Referer_check_domain); //リファラチェック実行

	//変数初期化
	$sendmail = 0;
	$empty_flag = 0;
	$post_mail = '';
	$errm = '';
	$header = '';
	$confirmDisplay = false;

	$requireResArray = requireCheck($require); //必須チェック実行し返り値を受け取る
	$errm = $requireResArray['errm'];
	$empty_flag = $requireResArray['empty_flag'];

	$recaptchaToken = $_POST['recaptcha_token'] ?? '';


	if (!empty($recaptchaSecret)) {
		$recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
		$response = file_get_contents($recaptchaUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaToken);
		if ($response === false) {
			$errm .= "<p class=\"error_messe\">reCAPTCHA認証に失敗しました。時間をおいて再度お試しください。</p>\n";
			$empty_flag = 1;
		} else {
			$responseKeys = json_decode($response, true);
			$isRecaptchaValid = !empty($responseKeys["success"]) && isset($responseKeys["score"]) && $responseKeys["score"] >= 0.5;
			if (!$isRecaptchaValid) {
				$errm .= "<p class=\"error_messe\">スパム判定されました。フォームの送信に失敗しました。</p>\n";
				$empty_flag = 1;
			}
		}
	}
	//メールアドレスチェック
	if (empty($errm)) {
		foreach ($_POST as $key => $val) {
			if ($val == "confirm_submit") $sendmail = 1;
			if ($key == $Email) $post_mail = h($val);
			if ($key == $Email && $mail_check == 1 && !empty($val)) {
				if (!checkMail($val)) {
					$errm .= "<p class=\"error_messe\">【" . $key . "】はメールアドレスの形式が正しくありません。</p>\n";
					$empty_flag = 1;
				}
			}
		}
	}

	if (($confirmDsp == 0 || $sendmail == 1) && $empty_flag != 1) {
		// トークンチェック（CSRF対策）※確認画面がONの場合のみ実施
		if ($useToken == 1 && $confirmDsp == 1) {
			if (empty($_SESSION['mailform_token']) || ($_SESSION['mailform_token'] !== $_POST['mailform_token'])) {
				exit('ページ遷移が不正です');
			}
			if (isset($_SESSION['mailform_token'])) unset($_SESSION['mailform_token']); //トークン破棄
			if (isset($_POST['mailform_token'])) unset($_POST['mailform_token']); //トークン破棄
		}

		//差出人に届くメールをセット
		if ($remail == 1) {
			$userBody = mailToUser($_POST, $form_setting['userMail']['name'], $remail_text, $mailFooterDsp, $mailSignature, $encode);
			$reheader = userHeader($form_setting['userMail']['fromName'], $form_setting['userMail']['from'], $encode);
			$re_subject = "=?iso-2022-jp?B?" . base64_encode(mb_convert_encoding($form_setting['userMail']['subject'], "JIS", $encode)) . "?=";
		}
		//管理者宛に届くメールをセット
		$adminBody = mailToAdmin($_POST, $form_setting['adminMail']['subject'], $mailFooterDsp, $mailSignature, $encode);
		$header = adminHeader($post_mail, $form_setting['adminMail']['bcc']);
		$subject = "=?iso-2022-jp?B?" . base64_encode(mb_convert_encoding($form_setting['adminMail']['subject'], "JIS", $encode)) . "?=";

		//-fオプションによるエンベロープFrom（Return-Path）の設定(safe_modeがOFFの場合かつ上記設定がONの場合のみ実施)
		if ($use_envelope == 0) {
			mail($form_setting['adminMail']['to'], $subject, $adminBody, $header);
			if ($remail == 1 && !empty($post_mail)) mail($post_mail, $re_subject, $userBody, $reheader);
		} else {
			mail($form_setting['adminMail']['to'], $subject, $adminBody, $header, '-f' . $form_setting['adminMail']['from']);
			if ($remail == 1 && !empty($post_mail)) mail($post_mail, $re_subject, $userBody, $reheader, '-f' . $form_setting['userMail']['from']);
		}
	} else if ($confirmDsp == 1) {
		$confirmDisplay = true;
	}
	if (($jumpPage == 1 && $sendmail == 1) || $confirmDsp == 0) {
		if ($empty_flag == 1) {
			// 入力エラーのHTML
		} else {

			header("Location: " . $form_setting['thanks']);
		}
	}
}

//----------------------------------------------------------------------
//  関数定義(START)
//----------------------------------------------------------------------
//機種依存文字の変換
$replaceStr['before'] = array('①', '②', '③', '④', '⑤', '⑥', '⑦', '⑧', '⑨', '⑩', '№', '㈲', '㈱', '髙'); //変換前の文字
$replaceStr['after'] = array('(1)', '(2)', '(3)', '(4)', '(5)', '(6)', '(7)', '(8)', '(9)', '(10)', 'No.', '（有）', '（株）', '高'); //変換後の文字


function checkMail($str) {
	$mailaddress_array = explode('@', $str);
	if (preg_match("/^[\.!#%&\-_0-9a-zA-Z\?\/\+]+\@[!#%&\-_0-9a-zA-Z]+(\.[!#%&\-_0-9a-zA-Z]+)+$/", "$str") && count($mailaddress_array) == 2) {
		return true;
	} else {
		return false;
	}
}
function h($string) {
	global $encode;
	return htmlspecialchars($string, ENT_QUOTES, $encode);
}
function sanitize($arr) {
	if (is_array($arr)) {
		return array_map('sanitize', $arr);
	}
	return str_replace("\0", "", $arr);
}
//Shift-JISの場合に誤変換文字の置換関数
function sjisReplace($arr, $encode) {
	foreach ($arr as $key => $val) {
		$key = str_replace('＼', 'ー', $key);
		$resArray[$key] = $val;
	}
	return $resArray;
}
//送信メールにPOSTデータをセットする関数
function postToMail($arr) {
	global $hankaku, $hankaku_array, $form_setting;
	$resArray = '';
	foreach ($arr as $key => $val) {
		$out = '';
		if (is_array($val)) {
			foreach ($val as $key02 => $item) {
				//連結項目の処理
				if (is_array($item)) {
					$out .= connect2val($item);
				} else {
					$out .= $item . ', ';
				}
			}
			$out = rtrim($out, ', ');
		} else {
			$out = $val;
		} //チェックボックス（配列）追記ここまで

		if (version_compare(PHP_VERSION, '5.1.0', '<=')) { //PHP5.1.0以下の場合のみ実行（7.4でget_magic_quotes_gpcが非推奨になったため）
			if (get_magic_quotes_gpc()) {
				$out = stripslashes($out);
			}
		}

		//全角→半角変換
		if ($hankaku == 1) {
			$out = zenkaku2hankaku($key, $out, $hankaku_array);
		}
		if ($out != "confirm_submit" && $key != "httpReferer") {
			$name = "";
			foreach ($form_setting['input'] as $input) {
				if ($input["name"] == $key) {
					$name = $input["label"];
				} elseif ($key == 'your-add1') {
					if (!empty($arr['your-add1']) || !empty($arr['your-add2'])) {
						$name = '住所';
						$out = $arr['your-add1'] . $arr['your-add2'];
					}
				}
			}
			if (!empty($name)) {
				$resArray .= "【 " . $name . " 】 " . h($out) . "\n";
			}
		}
	}
	return $resArray;
}
//確認画面の入力内容出力用関数
function confirmOutput($arr) {
	global $hankaku, $hankaku_array, $useToken, $confirmDsp, $replaceStr;
	$html = '';
	foreach ($arr as $key => $val) {
		$out = '';
		if (is_array($val)) {
			foreach ($val as $key02 => $item) {
				//連結項目の処理
				if (is_array($item)) {
					$out .= connect2val($item);
				} else {
					$out .= $item . ', ';
				}
			}
			$out = rtrim($out, ', ');
		} else {
			$out = $val;
		} //チェックボックス（配列）追記ここまで

		if (version_compare(PHP_VERSION, '5.1.0', '<=')) { //PHP5.1.0以下の場合のみ実行（7.4でget_magic_quotes_gpcが非推奨になったため）
			if (get_magic_quotes_gpc()) {
				$out = stripslashes($out);
			}
		}

		//全角→半角変換
		if ($hankaku == 1) {
			$out = zenkaku2hankaku($key, $out, $hankaku_array);
		}

		$out = nl2br(h($out)); //※追記 改行コードを<br>タグに変換
		$key = h($key);
		$out = str_replace($replaceStr['before'], $replaceStr['after'], $out); //機種依存文字の置換処理

		$html .= "<tr><th>" . $key . "</th><td>" . $out;
		$html .= '<input type="hidden" name="' . $key . '" value="' . str_replace(array("<br />", "<br>"), "", $out) . '" />';
		$html .= "</td></tr>\n";
	}
	//トークンをセット
	if ($useToken == 1 && $confirmDsp == 1) {
		$token = sha1(uniqid(mt_rand(), true));
		$_SESSION['mailform_token'] = $token;
		$html .= '<input type="hidden" name="mailform_token" value="' . $token . '" />';
	}

	return $html;
}

//全角→半角変換
function zenkaku2hankaku($key, $out, $hankaku_array) {
	global $encode;
	if (is_array($hankaku_array) && function_exists('mb_convert_kana')) {
		foreach ($hankaku_array as $hankaku_array_val) {
			if ($key == $hankaku_array_val) {
				$out = mb_convert_kana($out, 'a', $encode);
			}
		}
	}
	return $out;
}
//配列連結の処理
function connect2val($arr) {
	$out = '';
	foreach ($arr as $key => $val) {
		if ($key === 0 || $val == '') { //配列が未記入（0）、または内容が空のの場合には連結文字を付加しない（型まで調べる必要あり）
			$key = '';
		} elseif (strpos($key, "円") !== false && $val != '' && preg_match("/^[0-9]+$/", $val)) {
			$val = number_format($val); //金額の場合には3桁ごとにカンマを追加
		}
		$out .= $val . $key;
	}
	return $out;
}

//管理者宛送信メールヘッダ
function adminHeader($post_mail, $BccMail) {
	global $form_setting;
	$from = $form_setting['adminMail']['from'];
	$from_add = $form_setting['adminMail']['fromadd'];

	$header = "From: ";
	if (!empty($post_mail) && $from_add == 1) {
		$header .= mb_encode_mimeheader('"' . $post_mail . '"') . " <" . $from . ">\n";
	} else {
		$header .= $from . "\n";
	}
	if ($BccMail != '') {
		$header .= "Bcc: $BccMail\n";
	}
	if (!empty($post_mail)) {
		$header .= "Reply-To: " . $post_mail . "\n";
	}
	$header .= "Content-Type:text/plain;charset=iso-2022-jp\nX-Mailer: PHP/" . phpversion();
	return $header;
}
//管理者宛送信メールボディ
function mailToAdmin($arr, $subject, $mailFooterDsp, $mailSignature, $encode, $confirmDsp = 1) {

	$remote_addr = $_SERVER["REMOTE_ADDR"] ?? getenv('REMOTE_ADDR') ?? '';
	$host = ($remote_addr) ? gethostbyaddr($remote_addr) : 'UNKNOWN';

	$adminBody = "ご担当者様\n\nWEBサイトにお問い合わせがありました。\n3営業日以内にご対応をお願いいたします。\n";
	$adminBody .= "＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n";
	$adminBody .= "[お問い合わせ内容]\n\n";
	$adminBody .= postToMail($arr); //POSTデータを関数からセット
	$adminBody .= "\n＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n";
	$adminBody .= "送信された日時：" . date("Y/m/d (D) H:i:s", time()) . "\n";
	$adminBody .= "送信者のIPアドレス：" . $remote_addr . "\n";
	$adminBody .= "送信者のホスト名：" . $host . "\n";
	$adminBody .= "問い合わせのページURL：" . @$arr['httpReferer'] . "\n";
	// if ($confirmDsp != 1) {
	// 	$adminBody .= "問い合わせのページURL：" . @$_SERVER['HTTP_REFERER'] . "\n";
	// } else {
	// 	$adminBody .= "問い合わせのページURL：" . @$arr['httpReferer'] . "\n";
	// }
	if ($mailFooterDsp == 1) $adminBody .= $mailSignature;
	return mb_convert_encoding($adminBody, "JIS", $encode);
}

//ユーザ宛送信メールヘッダ
function userHeader($refrom_name, $to, $encode) {
	$reheader = "From: ";
	if (!empty($refrom_name)) {
		$default_internal_encode = mb_internal_encoding();
		if ($default_internal_encode != $encode) {
			mb_internal_encoding($encode);
		}
		$reheader .= mb_encode_mimeheader($refrom_name) . " <" . $to . ">\nReply-To: " . $to;
	} else {
		$reheader .= "$to\nReply-To: " . $to;
	}
	$reheader .= "\nContent-Type: text/plain;charset=iso-2022-jp\nX-Mailer: PHP/" . phpversion();
	return $reheader;
}
//ユーザ宛送信メールボディ
function mailToUser($arr, $dsp_name, $remail_text, $mailFooterDsp, $mailSignature, $encode) {
	$userBody = '';
	if (isset($arr[$dsp_name])) $userBody = h($arr[$dsp_name]) . " 様\n";
	$userBody .= $remail_text;
	$userBody .= "\n＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n";
	$userBody .= postToMail($arr); //POSTデータを関数からセット
	$userBody .= "\n＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n";
	$userBody .= "送信日時：" . date("Y/m/d (D) H:i:s", time()) . "\n";
	if ($mailFooterDsp == 1) $userBody .= $mailSignature;
	return mb_convert_encoding($userBody, "JIS", $encode);
}
//必須チェック関数
function requireCheck($require) {
	$res['errm'] = '';
	$res['empty_flag'] = 0;
	foreach ($require as $requireVal) {
		$existsFalg = '';
		foreach ($_POST as $key => $val) {
			if ($key == $requireVal) {

				//連結指定の項目（配列）のための必須チェック
				if (is_array($val)) {
					$connectEmpty = 0;
					foreach ($val as $kk => $vv) {
						if (is_array($vv)) {
							foreach ($vv as $kk02 => $vv02) {
								if ($vv02 == '') {
									$connectEmpty++;
								}
							}
						}
					}
					if ($connectEmpty > 0) {
						$res['errm'] .= "<p class=\"error_messe\">【" . h($key) . "】は必須項目です。</p>\n";
						$res['empty_flag'] = 1;
					}
				}
				//デフォルト必須チェック
				elseif ($val == '') {
					$res['errm'] .= "<p class=\"error_messe\">【" . h($key) . "】は必須項目です。</p>\n";
					$res['empty_flag'] = 1;
				}

				$existsFalg = 1;
				break;
			}
		}
		if ($existsFalg != 1) {
			$res['errm'] .= "<p class=\"error_messe\">【" . $requireVal . "】が未選択です。</p>\n";
			$res['empty_flag'] = 1;
		}
	}

	return $res;
}
//リファラチェック
function refererCheck($Referer_check, $Referer_check_domain) {
	if ($Referer_check == 1 && !empty($Referer_check_domain)) {
		if (strpos($_SERVER['HTTP_REFERER'], $Referer_check_domain) === false) {
			return exit('<p align="center">リファラチェックエラー。フォームページのドメインとこのファイルのドメインが一致しません</p>');
		}
	}
}

	//----------------------------------------------------------------------
	//  関数定義(END)
	//----------------------------------------------------------------------
