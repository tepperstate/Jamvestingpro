@once
{{-- Shared ambient backgrounds + bubbles --}}
<div class="bg-grid" style="pointer-events: none !important;"></div>
<!-- Deep Mesh Gradients -->
<div style="position:fixed;inset:0;pointer-events:none;z-index:-1;overflow:hidden;background-color:#000000;">
    <div style="position:absolute;width:600px;height:600px;background:radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, rgba(0,0,0,0) 70%);top:-20%;left:-10%;filter:blur(60px);animation: drift 20s infinite alternate linear;"></div>
    <div style="position:absolute;width:500px;height:500px;background:radial-gradient(circle, rgba(255, 51, 51, 0.1) 0%, rgba(0,0,0,0) 70%);bottom:-10%;right:-10%;filter:blur(60px);animation: drift 25s infinite alternate-reverse linear;"></div>
    <div style="position:absolute;width:400px;height:400px;background:radial-gradient(circle, rgba(153, 0, 0, 0.08) 0%, rgba(0,0,0,0) 70%);top:40%;left:40%;filter:blur(80px);animation: pulse 15s infinite alternate linear;"></div>
</div>
<style>
@keyframes drift { 0% { transform: translate(0,0); } 100% { transform: translate(100px, 100px); } }
@keyframes pulse { 0% { opacity: 0.5; transform: scale(0.8); } 100% { opacity: 1; transform: scale(1.2); } }
</style>
<div style="position:fixed;inset:0;pointer-events:none;overflow:hidden;z-index:1">
    <div class="bub" style="width:6px;height:6px;left:10%;bottom:-6px;background:radial-gradient(circle at 30% 30%,rgba(0,255,133,.5),rgba(0,255,133,.1));box-shadow:0 0 12px rgba(0,255,133,.18);animation-delay:0s;animation-duration:14s"></div>
    <div class="bub" style="width:10px;height:10px;left:25%;bottom:-10px;background:radial-gradient(circle at 30% 30%,rgba(0,255,133,.35),rgba(0,255,133,.06));box-shadow:0 0 20px rgba(0,255,133,.1);animation-delay:2s;animation-duration:18s"></div>
    <div class="bub" style="width:4px;height:4px;left:40%;bottom:-4px;background:radial-gradient(circle at 30% 30%,rgba(0,255,133,.5),rgba(0,255,133,.1));box-shadow:0 0 8px rgba(0,255,133,.18);animation-delay:4s;animation-duration:12s"></div>
    <div class="bub" style="width:12px;height:12px;left:55%;bottom:-12px;background:radial-gradient(circle at 30% 30%,rgba(0,255,133,.28),rgba(0,255,133,.04));box-shadow:0 0 24px rgba(0,255,133,.08);animation-delay:1s;animation-duration:20s"></div>
    <div class="bub" style="width:7px;height:7px;left:70%;bottom:-7px;background:radial-gradient(circle at 30% 30%,rgba(0,255,133,.45),rgba(0,255,133,.08));box-shadow:0 0 14px rgba(0,255,133,.14);animation-delay:3s;animation-duration:16s"></div>
    <div class="bub" style="width:5px;height:5px;left:85%;bottom:-5px;background:radial-gradient(circle at 30% 30%,rgba(0,255,133,.5),rgba(0,255,133,.1));box-shadow:0 0 10px rgba(0,255,133,.16);animation-delay:5s;animation-duration:13s"></div>
</div>

@if(!request()->routeIs('home'))
<canvas id="ambient-canvas" style="position:fixed;inset:0;pointer-events:none;z-index:1;width:100vw;height:100vh;opacity:0.35;"></canvas>
<script>
(function() {
    const canvas = document.getElementById('ambient-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    let width = canvas.width = window.innerWidth;
    let height = canvas.height = window.innerHeight;
    
    const particles = [];
    const maxParticles = Math.min(60, Math.floor((width * height) / 18000));
    const connectionDist = 125;
    
    let mouse = { x: null, y: null, active: false };
    
    class Particle {
        constructor() {
            this.reset();
            this.x = Math.random() * width;
            this.y = Math.random() * height;
        }
        reset() {
            this.x = Math.random() * width;
            this.y = Math.random() * height;
            this.vx = (Math.random() - 0.5) * 0.45;
            this.vy = (Math.random() - 0.5) * 0.45;
            this.radius = Math.random() * 2 + 1;
            this.color = Math.random() > 0.4 ? 'rgba(255, 51, 51, 0.45)' : 'rgba(79, 70, 229, 0.35)';
        }
        update() {
            this.x += this.vx;
            this.y += this.vy;
            
            if (mouse.active && mouse.x !== null && mouse.y !== null) {
                const dx = this.x - mouse.x;
                const dy = this.y - mouse.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < 180) {
                    const force = (180 - dist) / 180;
                    this.x += (dx / dist) * force * 1.6;
                    this.y += (dy / dist) * force * 1.6;
                }
            }
            
            if (this.x < 0 || this.x > width || this.y < 0 || this.y > height) {
                this.reset();
            }
        }
        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = this.color;
            ctx.fill();
        }
    }
    
    for (let i = 0; i < maxParticles; i++) {
        particles.push(new Particle());
    }
    
    window.addEventListener('resize', () => {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    });
    
    window.addEventListener('mousemove', (e) => {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
        mouse.active = true;
    });
    
    window.addEventListener('mouseleave', () => {
        mouse.active = false;
    });
    
    function animate() {
        ctx.clearRect(0, 0, width, height);
        
        for (let i = 0; i < particles.length; i++) {
            particles[i].update();
            particles[i].draw();
        }
        
        ctx.lineWidth = 0.8;
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                
                if (dist < connectionDist) {
                    const alpha = (1 - dist / connectionDist) * 0.16;
                    ctx.strokeStyle = `rgba(255, 51, 51, ${alpha})`;
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.stroke();
                }
            }
        }
        
        requestAnimationFrame(animate);
    }
    
    animate();
})();
</script>
@endif
@endonce
