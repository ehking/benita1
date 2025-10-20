(function () {
    if (!document.getElementById('three-d-customizer')) {
        return;
    }

    const canvas = document.getElementById('three-d-preview');
    const updateButton = document.getElementById('three-d-customizer-update');
    const engravingInput = document.getElementById('three-d-engraving');
    const customizationField = document.getElementById('three-d-customization-data');

    let scene, camera, renderer, controls, activeMesh;

    function initScene() {
        scene = new THREE.Scene();
        scene.background = new THREE.Color(0xf8fafc);

        const aspect = canvas.clientWidth / canvas.clientHeight;
        camera = new THREE.PerspectiveCamera(45, aspect, 0.1, 100);
        camera.position.set(2.5, 2.2, 2.5);

        renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(canvas.clientWidth, canvas.clientHeight, false);

        const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 1.1);
        hemiLight.position.set(0, 1, 0);
        scene.add(hemiLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.65);
        directionalLight.position.set(5, 10, 7.5);
        scene.add(directionalLight);

        controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.target.set(0, 0.4, 0);

        const grid = new THREE.GridHelper(10, 10, 0xe5e7eb, 0xf1f5f9);
        grid.position.y = -0.5;
        scene.add(grid);

        loadModel(ThreeDStoreCustomizer.default_model);
        animate();
    }

    function loadModel(url) {
        const loader = new THREE.GLTFLoader();
        loader.load(
            url,
            (gltf) => {
                if (activeMesh) {
                    scene.remove(activeMesh);
                }
                activeMesh = gltf.scene;
                activeMesh.traverse((child) => {
                    if (child.isMesh) {
                        child.material = child.material.clone();
                        child.material.color.set(getSelectedColorHex());
                    }
                });
                scene.add(activeMesh);
            },
            undefined,
            (error) => {
                console.error('Could not load 3D model:', error);
            }
        );
    }

    function getSelectedValue(name) {
        const checked = document.querySelector(`input[name="${name}"]:checked`);
        return checked ? checked.value : '';
    }

    const defaultColorMap = {
        gold: '#facc15',
        silver: '#cbd5f5',
        rose: '#fda4af',
        black: '#0f172a'
    };

    function getSelectedColorHex() {
        const slug = getSelectedValue('three_d_color');
        if (!slug) {
            return '#cbd5f5';
        }
        return defaultColorMap[slug] || '#cbd5f5';
    }

    function animate() {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
    }

    function serializeConfiguration() {
        return JSON.stringify({
            color: getSelectedValue('three_d_color'),
            pattern: getSelectedValue('three_d_pattern'),
            engraving: engravingInput.value.trim()
        });
    }

    function applyCustomization() {
        if (activeMesh) {
            activeMesh.traverse((child) => {
                if (child.isMesh && child.material) {
                    child.material.color.set(getSelectedColorHex());
                }
            });
        }

        customizationField.value = serializeConfiguration();
    }

    function handleResize() {
        if (!renderer || !camera) {
            return;
        }
        const width = canvas.clientWidth;
        const height = canvas.clientHeight;
        renderer.setSize(width, height, false);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
    }

    window.addEventListener('resize', handleResize);

    updateButton.addEventListener('click', () => {
        applyCustomization();
        updateCustomizationOnServer();
    });

    function updateCustomizationOnServer() {
        const payload = new FormData();
        payload.append('action', 'three_d_store_customize');
        payload.append('nonce', ThreeDStoreCustomizer.nonce);
        payload.append('color', getSelectedValue('three_d_color'));
        payload.append('pattern', getSelectedValue('three_d_pattern'));
        payload.append('engraving', engravingInput.value.trim());

        fetch(ThreeDStoreCustomizer.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: payload
        })
            .then((response) => response.json())
            .then((data) => {
                if (!data || !data.success) {
                    throw new Error('Could not save customization.');
                }
                const message = data.data && data.data.message ? data.data.message : data.data;
                console.info('Customization updated', message);
            })
            .catch((error) => {
                console.error(error);
            });
    }

    initScene();
    applyCustomization();
})();
