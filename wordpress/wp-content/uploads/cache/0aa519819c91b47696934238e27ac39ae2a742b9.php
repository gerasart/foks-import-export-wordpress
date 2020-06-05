<header class="banner">
  <div class="container">
    <a class="brand" href="<?php echo e(home_url('/')); ?>"><?php echo e(get_bloginfo('name', 'display')); ?></a>
    <nav class="nav-primary">
        <?php if(Theme\Help::Menu()): ?>
            <ul class="header_menu">
                <?php $__currentLoopData = \Theme\Help::Menu(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $current = ( $item->object_id == get_the_id() ) ? 'current' : ''; ?>
                    <li class="header_menu-item <?php echo e($current); ?>">
                        <a class="nav__link" href="<?php echo e($item->url); ?>" title="<?php echo e($item->title); ?>"><?php echo e($item->title); ?></a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </nav>
  </div>
</header>
