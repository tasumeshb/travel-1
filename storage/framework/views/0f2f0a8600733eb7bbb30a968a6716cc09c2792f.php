<?php if(!empty($seo_meta)): ?>
    <?php if(isset($seo_meta['seo_index']) and $seo_meta['seo_index'] == 0): ?>
        <meta name="robots" content="noindex">
    <?php endif; ?>
    <?php
        $page_title = $seo_meta['seo_title'] ?? $seo_meta['service_title'] ?? $page_title ?? "";
        if(!empty($page_title) and empty($seo_meta['is_homepage'])){
            $page_title .= " - ".setting_item_with_lang('site_title' ,false,'travelbok.com');
        }
        if(empty($page_title)){
            $page_title = setting_item_with_lang('site_title' ,false,'travelbok.com');
        }

        $isServiceDetail = is_service_detail_page() && !empty($row ?? null);
        $shareImage = get_absolute_url(
            $isServiceDetail ? get_service_share_image($row) : get_site_share_image()
        );
        $shareUrl = $seo_meta['full_url'] ?? request()->fullUrl();
        $shareDescription = $seo_meta['seo_share']['facebook']['desc'] ?? $seo_meta['seo_desc'] ?? $seo_meta['service_desc'] ?? setting_item_with_lang("site_desc");
        $twitterDescription = $seo_meta['seo_share']['twitter']['desc'] ?? $shareDescription;
        $siteTitle = setting_item_with_lang('site_title', false, config('app.name'));
    ?>
    <title><?php echo $page_title;?></title>

    <meta name="description" content="<?php echo e($seo_meta['seo_desc'] ?? $seo_meta['service_desc'] ?? setting_item_with_lang("site_desc")); ?>"/>

    
    <meta property="og:url" content="<?php echo e($shareUrl); ?>"/>
    <meta property="og:type" content="website"/>
    <meta property="og:site_name" content="<?php echo e($siteTitle); ?>"/>
    <meta property="og:title" content="<?php echo e($page_title); ?>"/>
    <meta property="og:description" content="<?php echo e($shareDescription); ?>"/>
    <meta property="og:image" content="<?php echo e($shareImage); ?>"/>

    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($page_title); ?>"/>
    <meta name="twitter:description" content="<?php echo e($twitterDescription); ?>">
    <meta name="twitter:image" content="<?php echo e($shareImage); ?>">

    <link rel="canonical" href="<?php echo e($shareUrl); ?>"/>
<?php else: ?>
    <?php
        if(!empty($page_title)){
            $page_title .= " - ".setting_item_with_lang('site_title' ,false,'travelbok.com');
        }else{
            $page_title = setting_item_with_lang('site_title' ,false,'travelbok.com');
        }
        $fallbackShareImage = get_absolute_url(get_site_share_image());
        $siteTitle = setting_item_with_lang('site_title', false, config('app.name'));
    ?>
    <title><?php echo $page_title;?></title>
    <meta property="og:site_name" content="<?php echo e($siteTitle); ?>">
    <meta property="og:title" content="<?php echo e($siteTitle); ?>" />
    <meta property="og:description" content="<?php echo e(setting_item_with_lang('site_desc')); ?>" />
    <meta property="og:image" itemprop="image" content="<?php echo e($fallbackShareImage); ?>">
    <meta property="og:type" content="website" />
<?php endif; ?>


<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-K9YCLWJKKB">
</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-K9YCLWJKKB');
</script>

<?php /**PATH /var/www/html/travel/modules/Layout/parts/seo-meta.blade.php ENDPATH**/ ?>