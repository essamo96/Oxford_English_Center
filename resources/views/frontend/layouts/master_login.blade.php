<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/oxford/css/cool_login.css') }}">
    @yield('css')
    <title>@yield('titel')</title>
</head>
<body>
    <canvas id="login-canvas" style="position:fixed;inset:0;width:100%;height:100%;z-index:1;pointer-events:none;"></canvas>
    @yield('content')
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function () {
        var canvas = document.getElementById('login-canvas');
        if (!canvas) return;
        var ctx = canvas.getContext('2d');
        var W, H, particles = [];

        var COLORS = [
            'rgba(255,255,255,VAL)',
            'rgba(247,183,51,VAL)',
            'rgba(44,154,183,VAL)',
            'rgba(100,180,255,VAL)',
        ];

        function resize() {
            W = canvas.width  = window.innerWidth;
            H = canvas.height = window.innerHeight;
        }

        function drawStar(x, y, r, color) {
            ctx.save();
            ctx.translate(x, y);
            ctx.beginPath();
            for (var i = 0; i < 5; i++) {
                var outer = (Math.PI * 2 * i / 5) - Math.PI / 2;
                var inner = outer + Math.PI / 5;
                var ox = Math.cos(outer) * r, oy = Math.sin(outer) * r;
                var ix = Math.cos(inner) * (r * 0.42), iy = Math.sin(inner) * (r * 0.42);
                if (i === 0) ctx.moveTo(ox, oy); else ctx.lineTo(ox, oy);
                ctx.lineTo(ix, iy);
            }
            ctx.closePath();
            ctx.fillStyle = color;
            ctx.fill();
            ctx.restore();
        }

        function rnd(a, b) { return Math.random() * (b - a) + a; }
        function rndColor() { return COLORS[Math.floor(Math.random() * COLORS.length)]; }

        function createParticle() {
            var isStar = Math.random() > 0.45;
            return {
                x: rnd(0, W), y: rnd(0, H),
                r: isStar ? rnd(3, 8) : rnd(3, 9),
                vx: rnd(-0.35, 0.35), vy: rnd(-0.5, -0.12),
                alpha: rnd(0.15, 0.7),
                color: rndColor(),
                type: isStar ? 'star' : 'ball',
                pulse: rnd(0, Math.PI * 2),
                pulseSpeed: rnd(0.012, 0.028),
            };
        }

        function init() {
            resize();
            particles = [];
            var count = Math.max(35, Math.min(Math.floor((W * H) / 9000), 90));
            for (var i = 0; i < count; i++) particles.push(createParticle());
        }

        function draw() {
            ctx.clearRect(0, 0, W, H);
            particles.forEach(function(p) {
                p.pulse += p.pulseSpeed;
                var a = p.alpha * (0.6 + 0.4 * Math.sin(p.pulse));
                var color = p.color.replace('VAL', a);

                if (p.type === 'ball') {
                    var grd = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r);
                    grd.addColorStop(0, color);
                    grd.addColorStop(1, p.color.replace('VAL', 0));
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                    ctx.fillStyle = grd;
                    ctx.fill();
                } else {
                    drawStar(p.x, p.y, p.r, color);
                }

                p.x += p.vx; p.y += p.vy;
                if (p.y < -20)  { p.y = H + 20; p.x = rnd(0, W); }
                if (p.x < -20)  p.x = W + 20;
                if (p.x > W+20) p.x = -20;
            });
            requestAnimationFrame(draw);
        }

        window.addEventListener('resize', init);
        init(); draw();
    })();
    </script>
    @yield('js')
</body>
</html>