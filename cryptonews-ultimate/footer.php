<footer class="site-footer">
<div class="container">
<div class="footer-widgets">
<?php for($i=1;$i<=4;$i++):?>
<div><?php if(is_active_sidebar('footer-'.$i)){dynamic_sidebar('footer-'.$i);}else{echo'<div class="footer-widget"><h3 data-translate="footer_links">Links</h3><ul><li><a href="#" data-translate="footer_about">About</a></li><li><a href="#" data-translate="footer_contact">Contact</a></li></ul></div>';}?></div>
<?php endfor;?>
</div>
<div class="footer-bottom">
<p>&copy; <?php echo date('Y');?> <?php bloginfo('name');?>. <span data-translate="footer_powered">Powered by CryptoNews v4.3</span></p>
</div>
</div>
</footer>
<?php wp_footer();?>
</body>
</html>
