<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SYSTÈME GELÉ - PIRATAGE EN COURS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            overflow: hidden;
            font-family: 'Courier New', monospace;
            background: #000;
        }

        /* Image de fond plein écran */
        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            z-index: 1;
            opacity: 0.6;
            filter: blur(1px) brightness(0.9);
        }

        /* Canvas pour l'effet Matrix */
        #matrix-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 2;
            pointer-events: none;
        }

        /* Overlay sombre */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.7);
            z-index: 3;
        }

        /* Texte principal */
        .main-text {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 4;
            text-align: center;
            color: #00ff00;
            text-shadow: 0 0 20px #00ff00, 0 0 40px #00ff00, 0 0 60px #00ff00;
            animation: glitch 2s infinite, pulse 1.5s infinite;
        }

        .main-text h1 {
            font-size: clamp(2rem, 8vw, 6rem);
            font-weight: bold;
            letter-spacing: 0.2em;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }

        .main-text .subtitle {
            font-size: clamp(1rem, 4vw, 2rem);
            color: #ff0000;
            text-shadow: 0 0 10px #ff0000, 0 0 20px #ff0000;
            animation: blink 1s infinite;
        }

        /* Animations */
        @keyframes glitch {
            0%, 100% { transform: translate(-50%, -50%); }
            25% { transform: translate(-50%, -50%) translate(2px, 0); }
            50% { transform: translate(-50%, -50%) translate(-2px, 0); }
            75% { transform: translate(-50%, -50%) translate(1px, 0); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }

        /* Indicateurs de statut */
        .status-indicators {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 4;
            color: #00ff00;
            font-size: clamp(0.8rem, 2vw, 1.2rem);
            line-height: 1.6;
        }

        .status-line {
            opacity: 0;
            animation: typewriter 4s infinite;
        }

        .status-line:nth-child(1) { animation-delay: 0s; }
        .status-line:nth-child(2) { animation-delay: 1s; }
        .status-line:nth-child(3) { animation-delay: 2s; }

        @keyframes typewriter {
            0% { opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 1; }
            100% { opacity: 0; }
        }

        /* Warning en bas */
        .warning {
            position: fixed;
            bottom: 50px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 4;
            color: #ff0000;
            font-size: clamp(0.9rem, 3vw, 1.5rem);
            text-align: center;
            animation: blink 0.5s infinite;
            text-shadow: 0 0 10px #ff0000;
        }

        /* Progress bar factice */
        .progress-container {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            z-index: 4;
            color: #00ff00;
            font-size: clamp(0.8rem, 2vw, 1rem);
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid #00ff00;
            margin-top: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #00ff00, #00aa00);
            width: 0%;
            animation: progress 10s infinite;
            box-shadow: 0 0 10px #00ff00;
        }

        @keyframes progress {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        /* Responsive mobile */
        @media (max-width: 768px) {
            .main-text h1 {
                font-size: 3rem;
                letter-spacing: 0.1em;
            }
            
            .main-text .subtitle {
                font-size: 1.2rem;
            }

            .status-indicators {
                font-size: 0.9rem;
                top: 10px;
                left: 10px;
            }

            .warning {
                bottom: 30px;
                font-size: 1rem;
            }

            .progress-container {
                bottom: 10px;
                left: 10px;
                right: 10px;
            }
        }

        @media (max-width: 480px) {
            .main-text h1 {
                font-size: 2.5rem;
            }
            
            .main-text .subtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Image de fond -->
    <img src="../src/img/20250920_1504_Marie Curie Fantôme_simple_compose_01k5kkh824fw08kknf9kvd8j0a.png" 
         alt="Background" class="background-image"/>
    
    <!-- Canvas pour l'effet Matrix -->
    <canvas id="matrix-canvas"></canvas>
    
    <!-- Overlay -->
    <div class="overlay"></div>
    
    <!-- Indicateurs de statut -->
    <div class="status-indicators">
        <div class="status-line">SYSTEM_STATUS: COMPROMISED</div>
        <div class="status-line">FIREWALL: DISABLED</div>
        <div class="status-line">ENCRYPTION: BYPASSED</div>
    </div>
    
    <!-- Texte principal -->
    <div class="main-text">
        <h1>PIRATAGE EN COURS</h1>
        <div class="subtitle">SYSTÈME GELÉ - ACCÈS REFUSÉ</div>
    </div>
    
    <!-- Warning -->
    <div class="warning">
        ⚠️ UNAUTHORIZED ACCESS DETECTED ⚠️
    </div>
    
    <!-- Progress bar -->
    <div class="progress-container">
        <div>EXTRACTING DATA...</div>
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
    </div>

    <script>
        // Effet Matrix avec canvas
        const canvas = document.getElementById('matrix-canvas');
        const ctx = canvas.getContext('2d');

        // Redimensionner le canvas
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }

        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        // Caractères Matrix (1 et 0)
        const chars = '01';
        const charArray = chars.split('');

        // Taille des colonnes
        const fontSize = 14;
        const columns = canvas.width / fontSize;

        // Array pour stocker la position Y de chaque goutte
        const drops = [];
        for (let x = 0; x < columns; x++) {
            drops[x] = Math.random() * canvas.height;
        }

        // Fonction de dessin
        function draw() {
            // Fond semi-transparent pour l'effet de traînée
            ctx.fillStyle = 'rgba(0, 0, 0, 0.04)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Style du texte
            ctx.fillStyle = '#00ff00';
            ctx.font = fontSize + 'px monospace';

            // Dessiner les caractères
            for (let i = 0; i < drops.length; i++) {
                // Caractère aléatoire
                const text = charArray[Math.floor(Math.random() * charArray.length)];
                
                // Position X
                const x = i * fontSize;
                // Position Y
                const y = drops[i] * fontSize;

                ctx.fillText(text, x, y);

                // Réinitialiser la goutte si elle atteint le bas
                if (y > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }

                // Incrémenter Y
                drops[i]++;
            }
        }

        // Animation
        setInterval(draw, 35);

        // Effets sonores simulés avec vibration (si supporté)
        function simulateHack() {
            if ('vibrate' in navigator) {
                navigator.vibrate([100, 50, 100]);
            }
        }

        // Déclencher la vibration toutes les 5 secondes
        setInterval(simulateHack, 5000);

        // Changer la couleur du texte aléatoirement
        function randomColorGlitch() {
            const mainText = document.querySelector('.main-text h1');
            const colors = ['#00ff00', '#ff0000', '#0099ff', '#ffff00'];
            const randomColor = colors[Math.floor(Math.random() * colors.length)];
            
            mainText.style.color = randomColor;
            mainText.style.textShadow = `0 0 20px ${randomColor}, 0 0 40px ${randomColor}, 0 0 60px ${randomColor}`;
            
            setTimeout(() => {
                mainText.style.color = '#00ff00';
                mainText.style.textShadow = '0 0 20px #00ff00, 0 0 40px #00ff00, 0 0 60px #00ff00';
            }, 200);
        }

        // Glitch aléatoire toutes les 3-7 secondes
        function scheduleRandomGlitch() {
            const delay = Math.random() * 4000 + 3000; // 3-7 secondes
            setTimeout(() => {
                randomColorGlitch();
                scheduleRandomGlitch();
            }, delay);
        }

        scheduleRandomGlitch();

        // Empêcher le clic droit et les raccourcis clavier
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>