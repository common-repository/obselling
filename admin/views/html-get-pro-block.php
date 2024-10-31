<?php

defined( 'ABSPATH' ) || exit;

if( !function_exists( 'obselling_pro_is_premium' ) || !obselling_pro_is_premium() ):
?>
<a href="<?php echo OBSELLING_WEBSITE_URL ?>" class="obselling-get-pro-block" target="_blank">
    <?php _e("Get PRO version for full options", 'obselling') ?>
</a>
<?php
endif;
?>
