

<?php $__env->startSection('title', __('messages.login')); ?>

<?php $__env->startSection('content'); ?>
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; background: linear-gradient(135deg, #0d0d0d, #1a1a1a);">
    <div class="card" style="max-width: 450px; width: 100%; border: 2px solid #ff8c00; box-shadow: 0 20px 60px rgba(255, 140, 0, 0.2);">
        <div class="card-header text-center" style="background: linear-gradient(135deg, #1a1a1a, #2a2a2a); border-bottom: 2px solid #ff8c00; padding: 40px 30px;">
            <h2 style="color: #ff8c00; margin: 0; display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 24px;">
                <i class="bi bi-briefcase-fill"></i> <?php echo e(__('messages.accounting_system')); ?>

            </h2>
            <p style="color: #999; margin-top: 10px; margin-bottom: 0; font-size: 14px;"><?php echo e(__('messages.login_to_continue')); ?></p>
        </div>

        <div class="card-body" style="padding: 40px 35px;">
            <!-- Language Toggle -->
            <div style="position: absolute; top: 20px; right: 20px;">
                <?php if(app()->getLocale() === 'ar'): ?>
                    <a href="<?php echo e(route('locale.switch', 'en')); ?>" class="btn btn-sm" style="background: #ff8c00; color: white; border-radius: 8px; border: none; text-decoration: none;">EN</a>
                <?php else: ?>
                    <a href="<?php echo e(route('locale.switch', 'ar')); ?>" class="btn btn-sm" style="background: #ff8c00; color: white; border-radius: 8px; border: none; text-decoration: none;">العربية</a>
                <?php endif; ?>
            </div>

            <!-- Error Messages -->
            <?php if($errors->any()): ?>
                <div class="alert alert-danger" style="background: #ffe8cc; color: #d32f2f; border-left: 4px solid #d32f2f; border-radius: 8px; margin-bottom: 20px;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div><i class="bi bi-exclamation-triangle"></i> <?php echo e($error); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger" style="background: #ffe8cc; color: #d32f2f; border-left: 4px solid #d32f2f; border-radius: 8px; margin-bottom: 20px;">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label" style="color: #1a1a1a; font-weight: 600; margin-bottom: 10px;"><?php echo e(__('messages.email')); ?></label>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('email')); ?>"
                        required
                        style="border: 2px solid #e0e0e0; border-radius: 8px; padding: 12px 15px; height: 48px; font-size: 14px;"
                        placeholder="admin@hamid.com"
                    >
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="display: block; color: #d32f2f; font-size: 13px; margin-top: 5px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label" style="color: #1a1a1a; font-weight: 600; margin-bottom: 10px;"><?php echo e(__('messages.password')); ?></label>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        required
                        style="border: 2px solid #e0e0e0; border-radius: 8px; padding: 12px 15px; height: 48px; font-size: 14px;"
                        placeholder="••••••••"
                    >
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="display: block; color: #d32f2f; font-size: 13px; margin-top: 5px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Remember Me -->
                <div class="form-check mb-3">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        name="remember" 
                        id="remember"
                        style="width: 18px; height: 18px; cursor: pointer; accent-color: #ff8c00; border: 2px solid #ddd;"
                    >
                    <label class="form-check-label" for="remember" style="cursor: pointer; color: #666; margin-left: 8px; margin-top: 2px;">
                        <?php echo e(__('messages.remember_me')); ?>

                    </label>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="btn w-100"
                    style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white; height: 48px; font-size: 15px; font-weight: 600; margin-top: 10px; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 25px rgba(255, 140, 0, 0.3)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                >
                    <i class="bi bi-box-arrow-in-right"></i> <?php echo e(__('messages.login')); ?>

                </button>
            </form>

            <!-- Demo Credentials Info -->
            <div style="background: #f5f5f5; border-left: 4px solid #27ae60; padding: 15px; border-radius: 8px; margin-top: 25px; font-size: 13px;">
                <p style="margin: 0 0 8px 0; color: #1a1a1a; font-weight: 600;">
                    <i class="bi bi-info-circle"></i> <?php echo e(__('messages.demo_credentials')); ?>

                </p>
                <p style="margin: 0 0 3px 0; color: #666;">
                    <strong>Email:</strong> admin@hamid.com
                </p>
                <p style="margin: 0; color: #666;">
                    <strong>Password:</strong> admin123456
                </p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    // Add focus animation to inputs
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#ff8c00';
            this.style.boxShadow = '0 0 0 3px rgba(255, 140, 0, 0.1)';
        });
        input.addEventListener('blur', function() {
            if(!this.classList.contains('is-invalid')) {
                this.style.borderColor = '#e0e0e0';
                this.style.boxShadow = 'none';
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.modern', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\accounting system web app\aktas-system\resources\views/auth/login.blade.php ENDPATH**/ ?>