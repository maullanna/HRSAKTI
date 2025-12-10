<?php if (count($errors) > 0): ?>
<?php foreach ($errors->all() as $error): ?>
<div class="alert alert-danger alert-dismissible">
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
    <h4><i class='icon fa fa-warning'></i> Error!</h4>
        <?php echo e($error); ?>
    
</div>
<?php endforeach; ?>
<?php endif; ?>

<?php if (session('success')): ?>
<div class='alert alert-success alert-dismissible'>
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
    <h4><i class='icon fa fa-check'></i> Success!</h4>
    <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class='alert alert-danger alert-dismissible'>
        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
        <h4><i class='icon fa fa-warning'></i> Error!</h4>
        <?php echo e(session('error')); ?>
    </div>
<?php endif; ?>

<?php if (session('info')): ?>
<div class="alert alert-info">
    <p class="text-center"><i class="fa fa-exclamation fa-lg" aria-hidden="true"></i>
        <?php echo e(session('info')); ?>
    </p>
</div>


<?php endif; ?>
