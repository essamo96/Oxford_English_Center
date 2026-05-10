/**
 * Animated Particles Hero Background - Responsive Design
 * Reusable script for adding animated particle effects to hero sections
 * Optimized for all devices (Mobile, Tablet, Desktop)
 *
 * Usage:
 *   document.addEventListener('DOMContentLoaded', () => {
 *       initParticles('canvas-id', {
 *           color1: 'rgba(245, 197, 24, 0.7)',
 *           color2: 'rgba(255,255,255,0.25)',
 *           count: 60,
 *           speed: 0.4,
 *           connectLines: true,
 *           lineColor: 'rgba(245, 197, 24, 0.15)'
 *       });
 *   });
 */

function initParticles(canvasId, options = {}) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) {
        console.warn(`Canvas with ID "${canvasId}" not found.`);
        return;
    }

    const ctx = canvas.getContext('2d');

    // Detect device type and get responsive values
    function getDeviceSettings() {
        const width = window.innerWidth;

        if (width <= 480) {
            // Mobile (Small)
            return {
                count: 25,
                speed: 0.25,
                minSize: 1.5,
                maxSize: 3.5,
                connectDistance: 80,
                speedX: 0.2,
                opacity: { min: 0.3, max: 0.6 }
            };
        } else if (width <= 768) {
            // Tablet
            return {
                count: 40,
                speed: 0.3,
                minSize: 2,
                maxSize: 4.5,
                connectDistance: 100,
                speedX: 0.3,
                opacity: { min: 0.35, max: 0.7 }
            };
        } else {
            // Desktop
            return {
                count: 60,
                speed: 0.4,
                minSize: 2,
                maxSize: 6,
                connectDistance: 150,
                speedX: 0.5,
                opacity: { min: 0.2, max: 0.8 }
            };
        }
    }

    const deviceSettings = getDeviceSettings();

    // Default options with device-aware overrides
    const config = {
        color1: options.color1 || 'rgba(245, 197, 24, 0.7)',
        color2: options.color2 || 'rgba(255, 255, 255, 0.25)',
        count: options.count || deviceSettings.count,
        speed: options.speed || deviceSettings.speed,
        connectLines: options.connectLines !== undefined ? options.connectLines : true,
        lineColor: options.lineColor || 'rgba(245, 197, 24, 0.15)',
        connectDistance: options.connectDistance || deviceSettings.connectDistance,
        minSize: deviceSettings.minSize,
        maxSize: deviceSettings.maxSize,
        speedX: deviceSettings.speedX,
        opacityRange: deviceSettings.opacity
    };

    // Set canvas size
    function resizeCanvas() {
        canvas.width = canvas.parentElement.offsetWidth;
        canvas.height = canvas.parentElement.offsetHeight;
    }

    // Particle class - Optimized for responsiveness
    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = canvas.height + Math.random() * 100; // Start below canvas
            this.size = Math.random() * (config.maxSize - config.minSize) + config.minSize;
            this.speedY = (Math.random() * config.speed) + 0.15;
            this.speedX = (Math.random() - 0.5) * config.speedX;
            this.color = Math.random() > 0.5 ? config.color1 : config.color2;
            this.opacity = Math.random() * (config.opacityRange.max - config.opacityRange.min) + config.opacityRange.min;
            this.originalOpacity = this.opacity;
            this.pulseAmount = 0;
            this.pulseDirection = 1;
        }

        update() {
            this.y -= this.speedY;
            this.x += this.speedX;

            // Subtle pulsing effect
            this.pulseAmount += 0.02 * this.pulseDirection;
            if (this.pulseAmount > 0.3 || this.pulseAmount < -0.3) {
                this.pulseDirection *= -1;
            }
            this.opacity = this.originalOpacity + (this.pulseAmount * 0.1);

            // Wrap around horizontally
            if (this.x < 0) this.x = canvas.width;
            if (this.x > canvas.width) this.x = 0;

            // Remove if above canvas
            if (this.y < -this.size) {
                return false; // Mark for removal
            }
            return true;
        }

        draw() {
            ctx.fillStyle = this.color;
            ctx.globalAlpha = Math.max(0, this.opacity);
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
            ctx.globalAlpha = 1;
        }
    }

    // Create particles array
    let particles = [];

    // Initialize particles
    function initializeParticles() {
        particles = [];
        for (let i = 0; i < config.count; i++) {
            particles.push(new Particle());
        }
    }

    // Draw connecting lines between nearby particles (optimized for mobile)
    function drawConnections() {
        if (!config.connectLines) return;

        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < config.connectDistance) {
                    ctx.strokeStyle = config.lineColor;
                    ctx.globalAlpha = (1 - distance / config.connectDistance) * 0.4;
                    ctx.lineWidth = window.innerWidth <= 768 ? 0.8 : 1;
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.stroke();
                    ctx.globalAlpha = 1;
                }
            }
        }
    }

    // Animation loop
    function animate() {
        // Clear canvas
        ctx.fillStyle = 'transparent';
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Update and draw particles
        for (let i = particles.length - 1; i >= 0; i--) {
            if (!particles[i].update()) {
                // Replace particle that went off-screen
                particles.splice(i, 1);
                particles.push(new Particle());
            } else {
                particles[i].draw();
            }
        }

        // Draw connections
        drawConnections();

        // Continue animation
        requestAnimationFrame(animate);
    }

    // Handle window resize with device-aware reconfiguration
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            resizeCanvas();
            // Recalibrate device settings on resize (for mobile orientation change)
            const newDeviceSettings = getDeviceSettings();
            config.count = newDeviceSettings.count;
            config.speed = newDeviceSettings.speed;
            config.connectDistance = newDeviceSettings.connectDistance;
            config.minSize = newDeviceSettings.minSize;
            config.maxSize = newDeviceSettings.maxSize;
            config.speedX = newDeviceSettings.speedX;
            config.opacityRange = newDeviceSettings.opacity;
            initializeParticles();
        }, 250);
    });

    // Initialize
    resizeCanvas();
    initializeParticles();
    animate();

    // Return control object for potential manipulation
    return {
        setParticleCount: (newCount) => {
            config.count = newCount;
            initializeParticles();
        },
        setSpeed: (newSpeed) => {
            config.speed = newSpeed;
        },
        setConnectDistance: (newDistance) => {
            config.connectDistance = newDistance;
        },
        pause: () => {
            // Can be extended with pause/resume functionality
        }
    };
}
