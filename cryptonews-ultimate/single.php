<?php get_header();?>
<main class="site-main">
<div class="container">
<?php if(have_posts()):while(have_posts()):the_post();
$word_count=str_word_count(wp_strip_all_tags(get_the_content()));
$reading_time=max(1,ceil($word_count/200));
$categories=get_the_category();
?>
<article <?php post_class('single-article');?>>
<section class="single-hero">
<div class="single-hero-content">
<a class="single-back" href="<?php echo esc_url(home_url('/'));?>">← <span data-translate="back_to_news">Back to news</span></a>
<h1 class="single-title"><?php the_title();?></h1>
<?php if(has_excerpt()):?>
<p class="single-subtitle"><?php echo esc_html(get_the_excerpt());?></p>
<?php endif;?>
<div class="single-meta">
<span class="meta-chip">📅 <span class="post-date" data-date="<?php echo get_the_date('d.m.Y');?>"><?php echo get_the_date('d.m.Y');?></span></span>
<span class="meta-chip">🕐 <span class="post-time"><?php echo get_the_time('H:i');?></span></span>
<span class="meta-chip">⏱ <?php echo esc_html($reading_time);?> min</span>
<span class="meta-chip">👁️ <span class="view-count"><?php echo cryptonews_get_views(get_the_ID());?></span> <span data-translate="views">views</span></span>
<?php cryptonews_display_rating(get_the_ID(),true);?>
</div>
<?php if($categories):?>
<div class="single-tags">
<?php foreach($categories as $category):?>
<a class="single-tag" href="<?php echo esc_url(get_category_link($category->term_id));?>"><?php echo esc_html($category->name);?></a>
<?php endforeach;?>
</div>
<?php endif;?>
</div>
<?php if(has_post_thumbnail()):?>
<div class="single-hero-media"><?php the_post_thumbnail('full');?></div>
<?php endif;?>
</section>
<div class="single-layout">
<div class="single-content-wrap">
<div class="single-content" id="postContent" data-original-lang="en"><?php the_content();?></div>
</div>
<aside class="single-aside">
<div class="single-aside-card">
<h3 class="single-aside-title" data-translate="article_stats">Article stats</h3>
<ul class="single-stats">
<li><span data-translate="published">Published</span> <strong><?php echo get_the_date('d.m.Y');?></strong></li>
<li><span data-translate="time">Time</span> <strong><?php echo get_the_time('H:i');?></strong></li>
<li><span data-translate="reading_time">Reading time</span> <strong><?php echo esc_html($reading_time);?> min</strong></li>
<li><span data-translate="views">Views</span> <strong><?php echo cryptonews_get_views(get_the_ID());?></strong></li>
</ul>
</div>
<?php if($categories):?>
<div class="single-aside-card">
<h3 class="single-aside-title" data-translate="topics">Topics</h3>
<div class="single-tags">
<?php foreach($categories as $category):?>
<a class="single-tag" href="<?php echo esc_url(get_category_link($category->term_id));?>"><?php echo esc_html($category->name);?></a>
<?php endforeach;?>
</div>
</div>
<?php endif;?>
</aside>
</div>
</article>
<?php cryptonews_social_share();?>
<?php
$related=cryptonews_related_posts(get_the_ID(),3);
if($related&&$related->have_posts()):
?>
<div class="related-posts">
<h3 class="related-title" data-translate="read_also">📰 Read Also</h3>
<div class="related-grid">
<?php while($related->have_posts()):$related->the_post();?>
<a href="<?php the_permalink();?>" class="related-item">
<?php if(has_post_thumbnail()):?>
<div class="related-thumb"><?php the_post_thumbnail('related-thumb');?></div>
<?php endif;?>
<div class="related-info">
<h4 class="related-item-title"><?php the_title();?></h4>
</div>
</a>
<?php endwhile;?>
</div>
</div>
<?php endif;wp_reset_postdata();?>
<?php endwhile;endif;?>
</div>
</main>
<?php get_footer();?>
