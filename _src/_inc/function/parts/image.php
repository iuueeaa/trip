<?php
$valueKey = "image";

/*
* HTML　
*/
function setHtmlImage($body = array(), $class = "p-image", $size = "medium", $breakpoint = "sp") {
  global $valueFormat;
  $body = (!empty($body)) ? $body : $valueFormat['image'];
  $image_src = $body['sizes'][$size];
  $image_title = $body['title'];
  $has_sp = !empty($body['image_sp']);
  if ($has_sp) {
    $image_src_sp = $body['image_sp']['sizes'][$size];
    $classes  = getResponsiveClasses($breakpoint);
    $pc_class = $classes['pc'];
    $sp_class = $classes['sp'];
  } ?>

  <figure class="<?php echo $class; ?>">
    <?php if ($has_sp) : ?>
      <img class="<?php echo $pc_class; ?>" src="<?php echo $image_src; ?>" alt="<?php echo $image_title; ?>">
      <img class="<?php echo $sp_class; ?>" src="<?php echo $image_src_sp; ?>" alt="<?php echo $image_title; ?>">
    <?php else : ?>
      <img src="<?php echo $image_src; ?>" alt="<?php echo $image_title; ?>">
    <?php endif; ?>
    <?php if (!empty($image_title)) : ?>
      <figcaption><?php echo $image_title; ?></figcaption>
    <?php endif; ?>
  </figure>
<?php }

function setHtmlBgImage($body = array(), $class = "p-image", $size = "medium", $breakpoint = "sp") {
  global $valueFormat, $image_path;
  $body = (!empty($body)) ? $body : $valueFormat['image'];
  $image_src = $body['sizes'][$size];
  $image_title = $body['title'];
  $has_sp = !empty($body['image_sp']);
  if ($has_sp) {
    $image_src_sp = $body['image_sp']['sizes'][$size];
    $classes  = getResponsiveClasses($breakpoint);
    $pc_class = $classes['pc'];
    $sp_class = $classes['sp'];
  }
?>
  <div class="<?php echo $class; ?> js-lazyImage"><?php if ($has_sp) : ?><span class="<?php echo $pc_class; ?> js-lazyImage__bgi" data-src="<?php echo $image_src ?>" style="background-image: url(<?php echo $image_path ?>common/space.webp);"><?php echo $image_title; ?></span><span class="<?php echo $sp_class; ?> js-lazyImage__bgi" data-src="<?php echo $image_src_sp ?>" style="background-image: url(<?php echo $image_path ?>common/space.webp);"><?php echo $image_title; ?></span><?php else : ?><span class="js-lazyImage__bgi" data-src="<?php echo $image_src ?>" style="background-image: url(<?php echo $image_path ?>common/space.webp);"><?php echo $image_title; ?></span><?php endif; ?></div>
<?php }


/*
* valueでの形
*/
$valueFormat[$valueKey] = setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル');

/*
* ACF設定用
*/

function setAcfImage($name = "image", $label = "画像", $logic = array()) {
  $array = array(
    'type' => 'group',
    'label' => $label,
    'name' => $name,
    'layout' => 'block',
    'sub_fields' => array(
      array(
        'type' => 'image',
        'label' => '画像',
        'name' => 'image',
      ),
      array(
        'type' => 'image',
        'label' => 'SP画像',
        'name' => 'image_sp',
      ),
    ),
  );
  $array = formatAcfLogic($array, $logic);
  return $array;
}
