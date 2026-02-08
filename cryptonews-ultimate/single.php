<?php get_header();?>
<main class="site-main">
<div class="container">
<?php if(have_posts()):while(have_posts()):the_post();?>
<article <?php post_class();?>>
<header class="single-header">
<h1 class="single-title"><?php the_title();?></h1>
<div class="single-meta">
<span>📅 <span class="post-date" data-date="<?php echo get_the_date('d.m.Y');?>"><?php echo get_the_date('d.m.Y');?></span></span>
<span>🕐 <span class="post-time"><?php echo get_the_time('H:i');?></span></span>
<span>👁️ <span class="view-count"><?php echo cryptonews_get_views(get_the_ID());?></span> <span data-translate="views">views</span></span>
<?php cryptonews_display_rating(get_the_ID(),true);?>
</div>
</header>
<?php if(has_post_thumbnail()):?>
<div class="single-thumbnail"><?php the_post_thumbnail('full');?></div>
<?php endif;?>
<div class="single-content" id="postContent" data-original-lang="en"><?php the_content();?></div>
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
