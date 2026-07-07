<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'variant' => 'light',
    'size' => 'md',
    'class' => ''
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'variant' => 'light',
    'size' => 'md',
    'class' => ''
]); ?>
<?php foreach (array_filter(([
    'variant' => 'light',
    'size' => 'md',
    'class' => ''
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $height = match($size) {
        'xs' => '24px',
        'sm' => '32px',
        'md' => '40px',
        'lg' => '56px',
        'xl' => '80px',
        default => '40px'
    };
    
    // Logo resolution
    $mainLogo = site()->logo;
    
    if ($variant === 'dark') {
        if ($mainLogo === 'logo.svg') {
            $logoPath = asset('storage/image/logo_dark.svg');
        } else {
            $logoPath = $mainLogo ? asset('storage/image/' . $mainLogo) : asset('assets/images/logo_dark.svg');
        }
    } else {
        $logoPath = $mainLogo ? asset('storage/image/' . $mainLogo) : asset('assets/images/logo.svg');
    }
?>

<img src="<?php echo e($logoPath); ?>" alt="<?php echo e(site()->name ?? 'Platform'); ?> Logo" style="height: <?php echo e($height); ?>; width: auto; object-fit: contain;" class="jv-logo <?php echo e($class); ?>">
<?php /**PATH C:\laragon\www\investors-p2bmarkets\resources\views/components/ui/logo.blade.php ENDPATH**/ ?>