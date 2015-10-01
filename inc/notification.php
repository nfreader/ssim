<?php if (isset($_GET['msg'])) : ?>
  <script>notify('<?php echo $_GET['msg']; ?>');</script>  
<?php endif; ?>
