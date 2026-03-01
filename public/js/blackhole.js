(function () {
    const container = document.getElementById('blackhole-bg');
    if (!container) return;

    const scene    = new THREE.Scene();
    const camera   = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 2000);
    camera.position.z = 200;

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    container.appendChild(renderer.domElement);

    function createStarTexture() {
        const canvas = document.createElement('canvas');
        canvas.width = canvas.height = 64;
        const ctx = canvas.getContext('2d');
        const grad = ctx.createRadialGradient(32, 32, 0, 32, 32, 32);
        grad.addColorStop(0,    'rgba(255, 255, 255, 1)');
        grad.addColorStop(0.15, 'rgba(210, 230, 255, 0.9)');
        grad.addColorStop(0.4,  'rgba(150, 190, 255, 0.4)');
        grad.addColorStop(1,    'rgba(0,   0,   0,   0)');
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, 64, 64);
        return new THREE.CanvasTexture(canvas);
    }

    const particleCount = 2000;
    const positions     = new Float32Array(particleCount * 3);
    const homePositions = new Float32Array(particleCount * 3);
    const velocities    = new Float32Array(particleCount * 3);

    for (let i = 0; i < particleCount; i++) {
        const i3 = i * 3;
        const x = (Math.random() - 0.5) * 1000;
        const y = (Math.random() - 0.5) * 1000;
        const z = (Math.random() - 0.5) * 400;

        positions[i3]     = homePositions[i3]     = x;
        positions[i3 + 1] = homePositions[i3 + 1] = y;
        positions[i3 + 2] = homePositions[i3 + 2] = z;
    }

    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

    const material = new THREE.PointsMaterial({
        map:             createStarTexture(),
        size:            3.5,
        sizeAttenuation: true,
        blending:        THREE.AdditiveBlending,
        depthWrite:      false,
        transparent:     true,
        opacity:         0.9,
    });

    scene.add(new THREE.Points(geometry, material));

    const mouse      = new THREE.Vector2();
    let mouseScreenX = -9999;
    let mouseScreenY = -9999;

    window.addEventListener('mousemove', (e) => {
        mouse.x      =  (e.clientX / window.innerWidth)  * 2 - 1;
        mouse.y      = -(e.clientY / window.innerHeight) * 2 + 1;
        mouseScreenX = e.clientX;
        mouseScreenY = e.clientY;
    });

    const tempVec   = new THREE.Vector3();
    const raycaster = new THREE.Raycaster();
    const attractor = new THREE.Vector3();

    const PROXIMITY_PX  = 200;
    const SPRING        = 0.06;
    const ATTRACT_FORCE = 0.25;
    const DAMPING       = 0.82;

    function animate() {
        requestAnimationFrame(animate);

        raycaster.setFromCamera(mouse, camera);
        attractor.copy(raycaster.ray.direction).multiplyScalar(300).add(camera.position);

        const pos = geometry.attributes.position.array;
        const hw  = window.innerWidth  / 2;
        const hh  = window.innerHeight / 2;

        for (let i = 0; i < particleCount; i++) {
            const i3 = i * 3;

            velocities[i3]     += (homePositions[i3]     - pos[i3])     * SPRING;
            velocities[i3 + 1] += (homePositions[i3 + 1] - pos[i3 + 1]) * SPRING;
            velocities[i3 + 2] += (homePositions[i3 + 2] - pos[i3 + 2]) * SPRING;

            tempVec.set(pos[i3], pos[i3 + 1], pos[i3 + 2]);
            tempVec.project(camera);
            const sx = ( tempVec.x + 1) * hw;
            const sy = (-tempVec.y + 1) * hh;

            const dsx        = mouseScreenX - sx;
            const dsy        = mouseScreenY - sy;
            const screenDist = Math.sqrt(dsx * dsx + dsy * dsy);

            if (screenDist < PROXIMITY_PX) {
                const t  = 1 - screenDist / PROXIMITY_PX;
                const dx = attractor.x - pos[i3];
                const dy = attractor.y - pos[i3 + 1];
                const dz = attractor.z - pos[i3 + 2];
                const d3 = Math.sqrt(dx * dx + dy * dy + dz * dz) + 0.1;

                const strength = t * ATTRACT_FORCE;
                velocities[i3]     += (dx / d3) * strength;
                velocities[i3 + 1] += (dy / d3) * strength;
                velocities[i3 + 2] += (dz / d3) * strength;
            }

            velocities[i3]     *= DAMPING;
            velocities[i3 + 1] *= DAMPING;
            velocities[i3 + 2] *= DAMPING;

            pos[i3]     += velocities[i3];
            pos[i3 + 1] += velocities[i3 + 1];
            pos[i3 + 2] += velocities[i3 + 2];
        }

        geometry.attributes.position.needsUpdate = true;
        renderer.render(scene, camera);
    }

    animate();

    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });
})();
