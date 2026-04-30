<?php
function setHtmlForm($body, $form_type, $class) {
	global $wpflg, $link_path, $form_setting, $recaptchaSite;
	$action = $body["setting"]['confirm'];
	$input = $body["setting"]['input'];
	$hasZip = !empty(array_filter($input, function ($f) {
		return ($f['type'] ?? '') === 'zip';
	}));
?>
	<div class="<?= $class ?>__input">
		<form method="post" action="<?= $action ?>" class="<?= $form_type ?>">
			<?php if ($form_type === "confirm") : ?>
				<input type="hidden" name="httpReferer" value="<?= h($_SERVER['HTTP_REFERER']) ?>">
				<input type="hidden" name="mail_set" value="confirm_submit">
				<?php
				$token = sha1(uniqid(mt_rand(), true));
				$_SESSION['mailform_token'] = $token;
				?>
				<input type="hidden" name="mailform_token" value="<?= $token ?>">
			<?php endif; ?>

			<div class="<?= $class ?>__input__wrap h-adr"><span class="p-country-name" style="display:none;">Japan</span>
				<div class="<?= $class ?>__input__dlwrap">
					<?php foreach ($input as $forminput):
						$req      = !empty($forminput["req"]) ? "req" : "";
						$dlclass  = $forminput["class"] ?? "";
						$type     = $forminput['type'] ?? 'text';
						$name     = $forminput['name'] ?? '';
						$label    = $forminput['label'] ?? '';
						$cap      = $forminput['cap'] ?? '';
						$inputlist = $forminput['inputlist'] ?? [];
						$count    = $forminput['count'] ?? '';
						$placeholder = $forminput['placeholder'] ?? '';
						$attrArray = [];
						foreach (['name', 'placeholder', 'id'] as $aaa) {
							if (!empty($forminput[$aaa])) $attrArray[] = $aaa . '="' . $forminput[$aaa] . '"';
						}
						if (!empty($count)) $attrArray[] = 'data-count="' . $count . '"';
						if (!empty($forminput['req'])) $attrArray[] = 'required aria-required="true"';
						$countclass = ($count != "") ? "countform" : "";
					?>
						<dl class="<?= $dlclass ?>">
							<dt class="<?= $req ?>"><span><?= $label ?></span></dt>
							<dd>

								<?php if ($form_type == "confirm"): ?>
									<?= renderConfirmField($type, $name) ?>
								<?php else: ?>
									<?= renderInputField($type, $name, $inputlist, $attrArray, '') ?>
								<?php endif; ?>

								<?php if ($count): ?>
									<p class="p-count"><span class="num">0</span><span>/ <?= $count ?>文字</span></p>
								<?php endif; ?>

								<?php if ($cap): ?>
									<ul class="p-cap">
										<?php foreach ($cap  as $captxt): ?>
											<li><span><?= $captxt ?></span></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</dd>
						</dl>
					<?php endforeach; ?>
				</div>
				<div class="<?= $class ?>__input__check">
					<p><a href="<?= $link_path ?>/privacy/"><span>個人情報保護方針</span></a>に同意の上送信をしてください。</p>
				</div>

				<div class="p-links">
					<div class="p-links__wrap">
						<?php if ($form_type == "confirm"): ?>
							<a class="<?= $class ?>__input__back" onClick="history.back()"><span>戻る</span></a>
							<button type="submit" name="submitConfirm" value="confirm" class="<?= $class ?>__input__button"><span>送信する</span></button>
						<?php else: ?>
							<button type="reset" name="リセット" value="button" class="<?= $class ?>__input__back"><span>リセット</span></button>
							<button type="submit" name="submitConfirm" value="confirm" class="<?= $class ?>__input__button"><span>同意の上確認画面へ進む</span></button>
						<?php endif; ?>
					</div>
				</div>
				<div class="<?= $class ?>__input__recaptchaText">
					<p class="p-text"><span>このサイトはGoogle reCAPTCHAで保護されています。<br>Googleの<a href="https://policies.google.com/privacy" target="_blank">プライバシーポリシー</a>並びに<a href="https://policies.google.com/terms" target="_blank">利用規約</a>が適用されます。</span></p>
				</div>
			</div>
			<input type="hidden" name="recaptcha_token" id="recaptcha_token" />
		</form>
		<?php
		if ($form_type == "input" && $hasZip): ?>
			<script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>
			<script>
				let search = document.getElementById('zipsearch');
				search.addEventListener('click', () => {
					let api = "https://zipcloud.ibsnet.co.jp/api/search?zipcode=";
					let input = document.getElementById('zip');
					let address = document.getElementById('address');
					let param = input.value.replace("-", ""); //入力された郵便番号から「-」を削除
					let url = api + param;
					fetch(url).then(function(response) {
						return response.text();
					}).then(function(text) {
						// 取得したデータをjson型に変換
						let text_json = JSON.parse(text)
						address.value = text_json.results[0].address1 + text_json.results[0].address2 + text_json.results[0].address3
					}).catch((reason) => {
						address.value = '';
						address.placeholder = "住所が存在しません。下の欄に住所を入力してください。";
					});
				});
			</script>
		<?php endif; ?>
		<?php if (!empty($recaptchaSite)): ?>
			<script src="https://www.google.com/recaptcha/api.js?render=<?php echo htmlspecialchars($recaptchaSite); ?>"></script>
			<script>
				grecaptcha.ready(function() {
					grecaptcha.execute('<?php echo htmlspecialchars($recaptchaSite); ?>', {
						action: 'submit'
					}).then(function(token) {
						var recaptchaInput = document.getElementById('recaptcha_token');
						if (recaptchaInput) recaptchaInput.value = token;
					});
				});
			</script>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * フォームinput部分をtypeごとに生成
 */
function renderInputField($type, $name, $inputlist, $attrArray, $value) {
	$attr = implode(' ', $attrArray);
	switch ($type) {
		case "select": ?>
			<div class="p-select"><span class="arrow"></span>
				<select name="<?= $name ?>" <?= $attr ?>>
					<option disabled value="" selected>選択してください</option>
					<?php foreach ($inputlist as $opt): ?>
						<option value="<?= $opt ?>"><?= $opt ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php break;
		case "radio": ?>
			<div class="p-radio">
				<?php foreach ($inputlist as $radio): ?>
					<span>
						<label>
							<input type="radio" name="<?= $name ?>" value="<?= $radio ?>" class="">
							<span><?= $radio ?></span>
						</label>
					</span>
				<?php endforeach; ?>
			</div>
		<?php break;
		case "checkbox": ?>
			<div class="p-checkbox">
				<?php foreach ($inputlist as $check): ?>
					<span>
						<label>
							<input type="checkbox" name="<?= $name ?>[]" value="<?= $check ?>" class="">
							<span><?= $check ?></span>
						</label>
					</span>
				<?php endforeach; ?>
			</div>
		<?php break;
		case "zip": ?>
			<div class="p-zip">
				<div class="p-input">
					<input type="text" name="<?= $name ?>" <?= $attr ?> size="8" maxlength="8" id="zip">
				</div>
				<div class="postal-search" id="zipsearch"><span>自動入力</span></div>
			</div>
		<?php break;
		case "add": ?>
			<div class="p-add">
				<div class="p-input">
					<input type="text" id="address" name="your-add1" placeholder="都道府県・市区町村" class="p-region p-locality p-street-address p-extended-address" autocomplete="name">
				</div>
				<div class="p-input">
					<input class="p-input" type="text" name="your-add2" placeholder="住所の続きを入力">
				</div>
			</div>
		<?php break;
		case "textarea": ?>
			<div class="p-textarea">
				<textarea name="<?= $name ?>" <?= $attr ?>></textarea>
			</div>
		<?php break;
		case "date": ?>
			<div class="p-input">
				<input type="date" name="<?= $name ?>" <?= $attr ?> class="hasDatepicker">
			</div>
		<?php break;
		default: ?>
			<div class="p-input">
				<input type="<?= $type ?>" name="<?= $name ?>" <?= $attr ?>>
			</div>
<?php break;
	}
}

/**
 * 確認画面用の値表示＋hidden生成
 */
function renderConfirmField($type, $name) {
	if ($type == "image") return ''; // 画像は特殊な時だけ
	if ($type == "checkbox" && !empty($_POST[$name])) {
		$val = $_POST[$name];
		if (is_array($val)) {
			$html = '<p>' . implode(", ", $val) . '</p>';
			foreach ($val as $v) {
				$html .= '<input type="hidden" name="' . $name . '[]" value="' . htmlspecialchars($v) . '">';
			}
			return $html;
		}
	}
	if ($name == "your-add") {
		return '<p>' . ($_POST['your-add1'] ?? '') . ($_POST['your-add2'] ?? '') . '</p>'
			. '<input type="hidden" name="your-add1" value="' . htmlspecialchars($_POST['your-add1'] ?? '') . '">'
			. '<input type="hidden" name="your-add2" value="' . htmlspecialchars($_POST['your-add2'] ?? '') . '">';
	}
	if (!empty($_POST[$name])) {
		return '<p>' . htmlspecialchars($_POST[$name]) . '</p><input type="hidden" name="' . $name . '" value="' . htmlspecialchars($_POST[$name]) . '">';
	}
	return '';
}
