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
<header style="margin-bottom:36px">
<h1 style="font-size:42px;font-weight:900"><span data-translate="search_title">Search</span>: "<?php echo get_search_query();?>"</h1>
<p style="color:#6b7280"><?php global $wp_query;echo $wp_query->found_posts;?> <span data-translate="results">results</span></p>
</header>
<div class="posts-grid">
<?php if(have_posts()):while(have_posts()):the_post();?>
<article <?php post_class('post-card');?>>
<?php if(has_post_thumbnail()):?>
<div class="post-thumbnail">
<a href="<?php the_permalink();?>"><?php the_post_thumbnail('large');?></a>
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
<p style="grid-column:1/-1;text-align:center;padding:60px" data-translate="no_results">No results found</p>
<?php endif;?>
</div>
</section>
</div>
</div>
</main>
<?php get_footer();?>
