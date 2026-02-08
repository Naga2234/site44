<!DOCTYPE html>
<html <?php language_attributes();?>>
<head>
<meta charset="<?php bloginfo('charset');?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="Latest cryptocurrency news, Bitcoin, Ethereum, blockchain updates">
<meta name="keywords" content="crypto, bitcoin, ethereum, blockchain, defi, nft">
<?php wp_head();?>
</head>
<body <?php body_class();?> data-lang="en">
<?php wp_body_open();?>
<header class="site-header">
<div class="crypto-ticker-wrap">
<div class="crypto-ticker" id="cryptoTicker">
<?php 
$cryptos=cryptonews_crypto_ticker();
$doubled=array_merge($cryptos,$cryptos);
foreach($doubled as $c):
$change_num=floatval($c['change']);
$price_value=floatval($c['price']);
$price_decimals=$price_value<1?6:2;
$is_up=$change_num>=0;
?>
<div class="ticker-item" data-crypto="<?php echo esc_attr($c['api']);?>">
<span class="ticker-symbol"><?php echo esc_html($c['symbol']);?></span>
<span class="ticker-price" data-price="<?php echo esc_attr($c['price']);?>">$<span class="price-value"><?php echo esc_html(number_format($price_value,$price_decimals));?></span></span>
<span class="ticker-change <?php echo $is_up?'up':'down';?>" data-change="<?php echo esc_attr($c['change']);?>"><span class="change-value"><?php echo esc_html(number_format($change_num,2));?></span>%</span>
</div>
<?php endforeach;?>
</div>
</div>
<div class="header-main">
<div class="container">
<div class="header-inner">
<div class="site-branding">
<?php if(has_custom_logo()){the_custom_logo();}else{?>
<a href="<?php echo esc_url(home_url('/'));?>" class="site-logo">Crypto<span>News</span></a>
<?php }?>
</div>
<div class="header-actions">
<form class="search-form" method="get" action="<?php echo esc_url(home_url('/'));?>">
<input type="search" name="s" placeholder="Search..." value="<?php echo get_search_query();?>" data-translate-placeholder="search">
<button type="submit">🔍</button>
</form>
<div class="lang-switcher" id="langSwitcher">
<button class="lang-current" id="langCurrent" type="button">
<span class="current-lang-code">EN</span>
</button>
<div class="lang-dropdown">
<button class="lang-btn active" data-lang="en" type="button">🇬🇧 EN</button>
<button class="lang-btn" data-lang="ru" type="button">🇷🇺 RU</button>
<button class="lang-btn" data-lang="es" type="button">🇪🇸 ES</button>
<button class="lang-btn" data-lang="de" type="button">🇩🇪 DE</button>
<button class="lang-btn" data-lang="fr" type="button">🇫🇷 FR</button>
</div>
</div>
<button class="theme-toggle" id="themeToggle" type="button" title="Toggle dark mode">🌙</button>
<button class="btn-menu">☰</button>
</div>
</div>
</div>
</div>
<div class="hashtag-nav">
<div class="container">
<div class="hashtag-list">
<?php foreach(cryptonews_hashtags() as $h):$active=get_query_var('tag')===$h['tag']?'active':'';?>
<a href="<?php echo home_url('/tag/'.$h['tag'].'/');?>" class="hashtag-item <?php echo $active;?>"><?php echo esc_html($h['label']);?></a>
<?php endforeach;?>
</div>
</div>
</div>
</header>
