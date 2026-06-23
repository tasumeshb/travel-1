@if(!empty($seo_meta))
    @if(isset($seo_meta['seo_index']) and $seo_meta['seo_index'] == 0)
        <meta name="robots" content="noindex">
    @endif
    @php
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
    @endphp
    <title><?php echo $page_title;?></title>

    <meta name="description" content="{{$seo_meta['seo_desc'] ?? $seo_meta['service_desc'] ?? setting_item_with_lang("site_desc")}}"/>

    {{-- Facebook / Open Graph --}}
    <meta property="og:url" content="{{ $shareUrl }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:site_name" content="{{ $siteTitle }}"/>
    <meta property="og:title" content="<?php echo e($page_title); ?>"/>
    <meta property="og:description" content="{{ $shareDescription }}"/>
    <meta property="og:image" content="{{ $shareImage }}"/>

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($page_title); ?>"/>
    <meta name="twitter:description" content="{{ $twitterDescription }}">
    <meta name="twitter:image" content="{{ $shareImage }}">

    <link rel="canonical" href="{{ $shareUrl }}"/>
@else
    @php
        if(!empty($page_title)){
            $page_title .= " - ".setting_item_with_lang('site_title' ,false,'travelbok.com');
        }else{
            $page_title = setting_item_with_lang('site_title' ,false,'travelbok.com');
        }
        $fallbackShareImage = get_absolute_url(get_site_share_image());
        $siteTitle = setting_item_with_lang('site_title', false, config('app.name'));
    @endphp
    <title><?php echo $page_title;?></title>
    <meta property="og:site_name" content="{{ $siteTitle }}">
    <meta property="og:title" content="{{ $siteTitle }}" />
    <meta property="og:description" content="{{ setting_item_with_lang('site_desc') }}" />
    <meta property="og:image" itemprop="image" content="{{ $fallbackShareImage }}">
    <meta property="og:type" content="website" />
@endif


<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-K9YCLWJKKB">
</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-K9YCLWJKKB');
</script>

