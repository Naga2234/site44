<?php get_header();?>
<main class="site-main">
<div class="container">
<div class="content-wrapper">
<aside class="popular-sidebar">
<h3 class="popular-title" data-translate="popular">🔥 Popular News</h3>
<ul class="popular-list">
<?php $popular=cryptonews_popular_posts(10);if($popular->have_posts()):while($popular->have_posts()):$popular->the_post();?>
<li class="popular-item">
<a href="<?php the_permalink();?>" class="popular-link"><?php the_title();?></a>
<div class="popular-meta">
<span>👁️ <span class="view-count"><?php echo cryptonews_get_views(get_the_ID());?></span> <span data-translate="views">views</span></span>
<span>🕐 <span class="post-time"><?php echo get_the_time('H:i');?></span></span>
</div>
</li>
<?php endwhile;wp_reset_postdata();endif;?>
</ul>
</aside>
<section class="posts-section">
<?php
$paged=get_query_var('paged')?get_query_var('paged'):1;
if($paged==1):
$featured_query=new WP_Query(array('posts_per_page'=>2,'paged'=>1));
if($featured_query->have_posts()):
?>
<div class="featured-posts">
<?php while($featured_query->have_posts()):$featured_query->the_post();?>
<article <?php post_class('featured-card');?>>
<?php if(has_post_thumbnail()):?>
<div class="post-thumbnail">
<a href="<?php the_permalink();?>"><?php the_post_thumbnail('large');?></a>
<span class="post-badge"><?php echo cryptonews_get_post_badge(get_the_ID());?></span>
</div>
<?php endif;?>
<div class="post-body">
<h3 class="post-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
<div class="post-excerpt"><?php echo wp_trim_words(get_the_excerpt(),30,'...');?></div>
<div class="post-meta">
<span>📅 <span class="post-date" data-date="<?php echo get_the_date('d.m.Y');?>"><?php echo get_the_date('d.m.Y');?></span></span>
<span>🕐 <span class="post-time"><?php echo get_the_time('H:i');?></span></span>
<span>👁️ <span class="view-count"><?php echo cryptonews_get_views(get_the_ID());?></span> <span data-translate="views">views</span></span>
<?php cryptonews_display_rating(get_the_ID());?>
</div>
</div>
</article>
<?php endwhile;?>
</div>
<?php endif;wp_reset_postdata();endif;?>
<div class="posts-grid">
<?php
$offset=$paged==1?2:0;
$regular_query=new WP_Query(array('posts_per_page'=>12,'paged'=>$paged,'offset'=>$offset));
if($regular_query->have_posts()):while($regular_query->have_posts()):$regular_query->the_post();
?>
<article <?php post_class('post-card');?>>
<?php if(has_post_thumbnail()):?>
<div class="post-thumbnail">
<a href="<?php the_permalink();?>"><?php the_post_thumbnail('large');?></a>
<span class="post-badge"><?php echo cryptonews_get_post_badge(get_the_ID());?></span>
</div>
<?php endif;?>
<div class="post-body">
<h3 class="post-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
<div class="post-excerpt"><?php echo wp_trim_words(get_the_excerpt(),20,'...');?></div>
<div class="post-meta">
<span>📅 <span class="post-date" data-date="<?php echo get_the_date('d.m.Y');?>"><?php echo get_the_date('d.m.Y');?></span></span>
<span>🕐 <span class="post-time"><?php echo get_the_time('H:i');?></span></span>
<span>👁️ <span class="view-count"><?php echo cryptonews_get_views(get_the_ID());?></span> <span data-translate="views">views</span></span>
<?php cryptonews_display_rating(get_the_ID());?>
</div>
</div>
</article>
<?php endwhile;else:?>
<p style="grid-column:1/-1;text-align:center;padding:40px" data-translate="no_posts">No posts found.</p>
<?php endif;wp_reset_postdata();?>
</div>
<?php cryptonews_pagination();?>
</section>
</div>
</div>
</main>
<?php get_footer();?>
