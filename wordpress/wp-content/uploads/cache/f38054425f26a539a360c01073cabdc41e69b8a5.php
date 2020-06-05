<!doctype html>
<html <?php echo get_language_attributes(); ?>>
<?php echo $__env->make('partials.default.head', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<body <?php body_class() ?>>
<?php do_action('get_header') ?>
<?php echo $__env->make('partials.default.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<div class="wrap" role="document">
  <div class="content">
    <main class="main">
      <?php echo $__env->yieldContent('content'); ?>
    </main>
    <?php if(App\display_sidebar()): ?>
      <aside class="sidebar">
        <?php echo $__env->make('partials.sidebar.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      </aside>
    <?php endif; ?>
  </div>
</div>

<?php if(isset($_GET['debug']) && $_GET['debug'] === 'contr'): ?>
  @hierarchy
<?php elseif(isset($_GET['debug']) && $_GET['debug'] === 'vars'): ?>
  <?php (new \Sober\Controller\Blade\Debugger(get_defined_vars())); ?>
  
<?php elseif(isset($_GET['debug']) && $_GET['debug'] === 'all'): ?>
  @hierarchy
  <?php (new \Sober\Controller\Blade\Debugger(get_defined_vars())); ?>
<?php endif; ?>

<?php do_action('get_footer') ?>
<?php echo $__env->make('partials.default.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php wp_footer() ?>
</body>
</html>