<?php $__env->startSection('title', __('Page Not Found')); ?>
<?php $__env->startSection('code', '404'); ?>
<?php $__env->startSection('message', __('Ooops! Looks like it is not here')); ?>

<?php echo $__env->make('errors::layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hootgpt-app/htdocs/app.hootgpt.com/resources/views/default/errors/404.blade.php ENDPATH**/ ?>