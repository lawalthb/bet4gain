<div id="preloader" class="fixed top-0 left-0 w-full h-full bg-gray-900 z-50 flex items-center justify-center">
    <div class="relative">
        <div id="preloader">
            <img src="{{ asset('assets/images/bet4gain-preload.png') }}" alt="Logo" width="200px" height="100px" />
        </div>

    </div>
</div>

<script>
    window.addEventListener('load', () => {
        const preloader = document.getElementById('preloader');
        preloader.style.opacity = '0';
        setTimeout(() => {
            preloader.style.display = 'none';
        }, 1000);
    });
</script>