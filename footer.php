</div><!-- end .box-wrap -->

<div class="footer">
    <?php if ($this->options->footerCopyright): ?>
        <?php echo $this->options->footerCopyright; ?>
        &nbsp;|&nbsp;
    <?php endif; ?>
    Theme by <a href="https://kehua.me">kehua</a>
</div>

<script src="<?php $this->options->themeUrl('js/jquery.min.js'); ?>"></script>
<script src="<?php $this->options->themeUrl('js/kehua_slider.js'); ?>"></script>
<script type="text/javascript">
   $(".slider-container").ikSlider({
      speed: 500,
      delay: 3000,
      infinite: true
   });
</script>

<?php $this->footer(); ?>
</body>
</html>

