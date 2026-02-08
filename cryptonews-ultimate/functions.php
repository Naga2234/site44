<?php
function cryptonews_setup(){add_theme_support('title-tag');add_theme_support('post-thumbnails');add_theme_support('html5',array('search-form','comment-form'));add_theme_support('custom-logo');register_nav_menus(array('primary'=>'Primary Menu','footer'=>'Footer Menu'));set_post_thumbnail_size(700,480,true);add_image_size('related-thumb',400,280,true);global $content_width;if(!isset($content_width))$content_width=880;}
add_action('after_setup_theme','cryptonews_setup');
function cryptonews_widgets_init(){register_sidebar(array('name'=>'Sidebar','id'=>'sidebar-1','before_widget'=>'<div class="widget">','after_widget'=>'</div>','before_title'=>'<h3 class="widget-title">','after_title'=>'</h3>'));for($i=1;$i<=4;$i++){register_sidebar(array('name'=>'Footer '.$i,'id'=>'footer-'.$i,'before_widget'=>'<div class="footer-widget">','after_widget'=>'</div>','before_title'=>'<h3>','after_title'=>'</h3>'));}}
add_action('widgets_init','cryptonews_widgets_init');
function cryptonews_scripts(){wp_enqueue_style('cryptonews-style',get_stylesheet_uri(),array(),'4.3.0');wp_enqueue_script('cryptonews-main',get_template_directory_uri().'/js/main.js',array('jquery'),'4.3.0',true);wp_localize_script('cryptonews-main','cryptoAjax',array('ajax_url'=>admin_url('admin-ajax.php'),'nonce'=>wp_create_nonce('crypto_vote_nonce')));}
add_action('wp_enqueue_scripts','cryptonews_scripts');
add_filter('excerpt_length',function(){return 20;});
add_filter('excerpt_more',function(){return '...';});
function cryptonews_get_views($post_id){$count=get_post_meta($post_id,'post_views',true);return $count?number_format($count):0;}
function cryptonews_set_views($post_id){$count=(int)get_post_meta($post_id,'post_views',true);update_post_meta($post_id,'post_views',$count+1);}
function cryptonews_track_views(){if(!is_single())return;global $post;if($post)cryptonews_set_views($post->ID);}
add_action('wp_head','cryptonews_track_views');
function cryptonews_get_rating($post_id){$rating=get_post_meta($post_id,'post_rating',true);return $rating?floatval($rating):0;}
function cryptonews_get_rating_count($post_id){$count=get_post_meta($post_id,'rating_count',true);return $count?intval($count):0;}
function cryptonews_display_rating($post_id,$interactive=false){$rating=cryptonews_get_rating($post_id);$count=cryptonews_get_rating_count($post_id);$full=floor($rating);$empty=5-$full;$stars='';if($interactive){for($i=1;$i<=5;$i++){$filled=$i<=$full?'★':'☆';$stars.='<span class="star" data-value="'.$i.'">'.$filled.'</span>';}echo'<div class="post-rating" data-post-id="'.$post_id.'"><span class="rating-stars interactive">'.$stars.'</span><span class="rating-count">('.$count.')</span></div>';}else{$stars=str_repeat('★',$full).str_repeat('☆',$empty);echo'<div class="post-rating"><span class="rating-stars">'.$stars.'</span><span class="rating-count">('.$count.')</span></div>';}}
function cryptonews_vote_rating(){check_ajax_referer('crypto_vote_nonce','nonce');$post_id=intval($_POST['post_id']);$vote=intval($_POST['vote']);if($post_id&&$vote>=1&&$vote<=5){$current_rating=floatval(get_post_meta($post_id,'post_rating',true));$current_count=intval(get_post_meta($post_id,'rating_count',true));$new_count=$current_count+1;$new_rating=(($current_rating*$current_count)+$vote)/$new_count;update_post_meta($post_id,'post_rating',round($new_rating,1));update_post_meta($post_id,'rating_count',$new_count);wp_send_json_success(array('rating'=>round($new_rating,1),'count'=>$new_count));}wp_send_json_error();}
add_action('wp_ajax_vote_rating','cryptonews_vote_rating');
add_action('wp_ajax_nopriv_vote_rating','cryptonews_vote_rating');
function cryptonews_auto_add_tags($post_id){if(wp_is_post_revision($post_id)||wp_is_post_autosave($post_id))return;$post=get_post($post_id);if(!$post||$post->post_type!=='post')return;$content=$post->post_title.' '.$post->post_content;$content=strtolower($content);$tag_keywords=array('bitcoin'=>array('bitcoin','btc'),'crypto'=>array('crypto','cryptocurrency'),'ethereum'=>array('ethereum','eth'),'blockchain'=>array('blockchain','block chain'),'defi'=>array('defi','decentralized finance'),'nft'=>array('nft','non-fungible'),'trading'=>array('trading','trade','trader'),'altcoin'=>array('altcoin','alt coin'),'mining'=>array('mining','miner'),'web3'=>array('web3','web 3'));$tags_to_add=array();foreach($tag_keywords as $tag=>$keywords){foreach($keywords as $keyword){if(strpos($content,$keyword)!==false){$tags_to_add[]=$tag;break;}}}if(!empty($tags_to_add)){wp_set_post_tags($post_id,$tags_to_add,false);}else{wp_set_post_tags($post_id,array('crypto'),false);}}
add_action('save_post','cryptonews_auto_add_tags',20);
function cryptonews_crypto_ticker(){
    $assets=array(
        array('symbol'=>'BTC','name'=>'Bitcoin','api'=>'bitcoin'),
        array('symbol'=>'ETH','name'=>'Ethereum','api'=>'ethereum'),
        array('symbol'=>'USDT','name'=>'Tether','api'=>'tether'),
        array('symbol'=>'BNB','name'=>'BNB','api'=>'binance-coin'),
        array('symbol'=>'XRP','name'=>'XRP','api'=>'xrp'),
        array('symbol'=>'SOL','name'=>'Solana','api'=>'solana'),
        array('symbol'=>'ADA','name'=>'Cardano','api'=>'cardano'),
        array('symbol'=>'DOGE','name'=>'Dogecoin','api'=>'dogecoin'),
        array('symbol'=>'TRX','name'=>'TRON','api'=>'tron'),
        array('symbol'=>'MATIC','name'=>'Polygon','api'=>'polygon'),
        array('symbol'=>'DOT','name'=>'Polkadot','api'=>'polkadot'),
        array('symbol'=>'LTC','name'=>'Litecoin','api'=>'litecoin'),
        array('symbol'=>'SHIB','name'=>'Shiba Inu','api'=>'shiba-inu'),
        array('symbol'=>'AVAX','name'=>'Avalanche','api'=>'avalanche'),
        array('symbol'=>'LINK','name'=>'Chainlink','api'=>'chainlink')
    );

    $ids=array_map(function($asset){return $asset['api'];},$assets);
    $cache_key='cryptonews_ticker_prices';
    $prices=get_transient($cache_key);

    if($prices===false){
        $response=wp_remote_get('https://api.coincap.io/v2/assets?ids='.implode(',',$ids),array('timeout'=>8));
        $prices=array();
        if(!is_wp_error($response)&&wp_remote_retrieve_response_code($response)===200){
            $body=json_decode(wp_remote_retrieve_body($response),true);
            if(isset($body['data'])&&is_array($body['data'])){
                foreach($body['data'] as $item){
                    if(isset($item['id'])){
                        $prices[$item['id']]=array(
                            'price'=>isset($item['priceUsd'])?(float)$item['priceUsd']:0,
                            'change'=>isset($item['changePercent24Hr'])?(float)$item['changePercent24Hr']:0
                        );
                    }
                }
            }
        }
        set_transient($cache_key,$prices,30);
    }

    foreach($assets as &$asset){
        $asset_data=isset($prices[$asset['api']])?$prices[$asset['api']]:array('price'=>0,'change'=>0);
        $asset['price']=$asset_data['price'];
        $asset['change']=$asset_data['change'];
    }
    unset($asset);

    return $assets;
}
function cryptonews_hashtags(){return array(array('tag'=>'bitcoin','label'=>'#bitcoin'),array('tag'=>'crypto','label'=>'#crypto'),array('tag'=>'altcoin','label'=>'#altcoin'),array('tag'=>'ethereum','label'=>'#ethereum'),array('tag'=>'blockchain','label'=>'#blockchain'),array('tag'=>'defi','label'=>'#defi'),array('tag'=>'nft','label'=>'#nft'),array('tag'=>'trading','label'=>'#trading'),array('tag'=>'mining','label'=>'#mining'),array('tag'=>'web3','label'=>'#web3'));}
function cryptonews_get_post_badge($post_id){$tags=get_the_tags($post_id);if($tags&&!is_wp_error($tags)){$first_tag=$tags[0];return '#'.esc_html($first_tag->name);}$cats=get_the_category($post_id);if($cats){return esc_html($cats[0]->name);}return 'News';}
function cryptonews_pagination(){global $wp_query;$big=999999999;$pages=paginate_links(array('base'=>str_replace($big,'%#%',esc_url(get_pagenum_link($big))),'format'=>'?paged=%#%','current'=>max(1,get_query_var('paged')),'total'=>$wp_query->max_num_pages,'type'=>'array','prev_text'=>'← Prev','next_text'=>'Next →'));if(is_array($pages)){echo'<div class="pagination">';foreach($pages as $page)echo $page;echo'</div>';}}
function cryptonews_popular_posts($count=10){$week_ago=date('Y-m-d',strtotime('-7 days'));return new WP_Query(array('posts_per_page'=>$count,'post_status'=>'publish','date_query'=>array(array('after'=>$week_ago)),'meta_key'=>'post_views','orderby'=>'meta_value_num','order'=>'DESC','ignore_sticky_posts'=>true));}
function cryptonews_related_posts($post_id,$count=3){$tags=wp_get_post_tags($post_id);if($tags){$tag_ids=array();foreach($tags as $tag){$tag_ids[]=$tag->term_id;}return new WP_Query(array('tag__in'=>$tag_ids,'post__not_in'=>array($post_id),'posts_per_page'=>$count,'orderby'=>'rand'));}return null;}
function cryptonews_social_share(){$url=get_permalink();$title=get_the_title();$encoded_url=urlencode($url);$encoded_title=urlencode($title);echo'<div class="social-share"><h3 class="social-share-title" data-translate="share">Share this article</h3><div class="social-buttons">';echo'<a href="https://www.facebook.com/sharer/sharer.php?u='.$encoded_url.'" target="_blank" rel="noopener" class="social-btn facebook" aria-label="Share on Facebook" title="Facebook"><svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>';echo'<a href="https://twitter.com/intent/tweet?url='.$encoded_url.'&text='.$encoded_title.'" target="_blank" rel="noopener" class="social-btn twitter" aria-label="Share on Twitter" title="Twitter"><svg viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg></a>';echo'<a href="https://t.me/share/url?url='.$encoded_url.'&text='.$encoded_title.'" target="_blank" rel="noopener" class="social-btn telegram" aria-label="Share on Telegram" title="Telegram"><svg viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg></a>';echo'<a href="https://wa.me/?text='.$encoded_title.'%20'.$encoded_url.'" target="_blank" rel="noopener" class="social-btn whatsapp" aria-label="Share on WhatsApp" title="WhatsApp"><svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg></a>';echo'<a href="https://www.linkedin.com/sharing/share-offsite/?url='.$encoded_url.'" target="_blank" rel="noopener" class="social-btn linkedin" aria-label="Share on LinkedIn" title="LinkedIn"><svg viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>';echo'</div></div>';}

function cryptonews_output_social_meta(){
    if(!is_singular('post'))return;
    $post_id=get_the_ID();
    if(!$post_id)return;
    $title=single_post_title('',false);
    $description=has_excerpt($post_id)?get_the_excerpt($post_id):wp_trim_words(wp_strip_all_tags(get_the_content(null,false,$post_id)),26,'...');
    $url=get_permalink($post_id);
    $image_url='';
    if(has_post_thumbnail($post_id)){
        $image_data=wp_get_attachment_image_src(get_post_thumbnail_id($post_id),'full');
        if(!empty($image_data[0]))$image_url=$image_data[0];
    }
    echo'<meta property="og:type" content="article">';
    echo'<meta property="og:title" content="'.esc_attr($title).'">';
    echo'<meta property="og:description" content="'.esc_attr($description).'">';
    echo'<meta property="og:url" content="'.esc_url($url).'">';
    if($image_url)echo'<meta property="og:image" content="'.esc_url($image_url).'">';
    echo'<meta name="twitter:card" content="'.($image_url?'summary_large_image':'summary').'">';
    echo'<meta name="twitter:title" content="'.esc_attr($title).'">';
    echo'<meta name="twitter:description" content="'.esc_attr($description).'">';
    if($image_url)echo'<meta name="twitter:image" content="'.esc_url($image_url).'">';
}
add_action('wp_head','cryptonews_output_social_meta',5);
