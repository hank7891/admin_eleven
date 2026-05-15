@extends('layouts.admin')

@section('title-suffix', ' · 貪食蛇')

@section('content')
    <x-admin.page-head
        title="貪食蛇小遊戲"
        subtitle="管理員放鬆專區"
        :breadcrumbs="[['label' => '首頁', 'url' => 'admin/'], ['label' => '貪食蛇']]"
    />

    <x-admin.card title="開始遊戲">
        <div class="admin-snake-wrap">
            <p class="admin-help admin-snake-tip">
                <span class="material-symbols-outlined" aria-hidden="true">info</span>
                <span>使用方向鍵 ↑ ↓ ← → 或 W A S D 控制蛇的移動，吃到食物可以增加分數和長度。</span>
            </p>

            <canvas id="gameCanvas" width="600" height="600" class="admin-snake-canvas"></canvas>

            <div class="admin-snake-stats">
                <div class="admin-snake-stat admin-snake-stat-score">
                    <p class="admin-snake-stat-label">分數</p>
                    <p class="admin-snake-stat-value" id="score">0</p>
                </div>
                <div class="admin-snake-stat admin-snake-stat-length">
                    <p class="admin-snake-stat-label">長度</p>
                    <p class="admin-snake-stat-value" id="length">3</p>
                </div>
                <div class="admin-snake-stat admin-snake-stat-best">
                    <p class="admin-snake-stat-label">最高分</p>
                    <p class="admin-snake-stat-value" id="highScore">0</p>
                </div>
            </div>

            <div class="admin-snake-controls">
                <button type="button" id="startBtn" class="admin-btn admin-btn-primary">
                    <span class="material-symbols-outlined" aria-hidden="true">play_arrow</span>
                    <span id="startBtnLabel">開始遊戲</span>
                </button>
                <button type="button" id="pauseBtn" class="admin-btn admin-btn-muted" disabled>
                    <span class="material-symbols-outlined" aria-hidden="true">pause</span>
                    <span id="pauseBtnLabel">暫停</span>
                </button>
                <button type="button" id="resetBtn" class="admin-btn admin-btn-danger">
                    <span class="material-symbols-outlined" aria-hidden="true">refresh</span>
                    <span>重新開始</span>
                </button>
            </div>

            <div class="admin-snake-difficulty">
                <span class="admin-text-sm admin-text-mute">難度：</span>
                <button type="button" class="admin-btn admin-btn-outline admin-btn-sm difficulty-btn" data-speed="150">簡單</button>
                <button type="button" class="admin-btn admin-btn-primary admin-btn-sm difficulty-btn is-active" data-speed="100">普通</button>
                <button type="button" class="admin-btn admin-btn-outline admin-btn-sm difficulty-btn" data-speed="60">困難</button>
            </div>
        </div>
    </x-admin.card>
@endsection

@push('scripts')
    <script>
        (function () {
            const canvas = document.getElementById('gameCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const gridSize = 20;
            const tileCount = canvas.width / gridSize;

            const scoreEl = document.getElementById('score');
            const lengthEl = document.getElementById('length');
            const highScoreEl = document.getElementById('highScore');
            const startBtn = document.getElementById('startBtn');
            const startBtnLabel = document.getElementById('startBtnLabel');
            const pauseBtn = document.getElementById('pauseBtn');
            const pauseBtnLabel = document.getElementById('pauseBtnLabel');
            const resetBtn = document.getElementById('resetBtn');
            const difficultyBtns = document.querySelectorAll('.difficulty-btn');

            let snake = [{ x: 10, y: 10 }];
            let velocityX = 0, velocityY = 0;
            let foodX = 15, foodY = 15;
            let score = 0, gameSpeed = 100, gameLoop = null;
            let isPaused = false, isGameOver = false;
            let highScore = parseInt(localStorage.getItem('snakeHighScore') || '0', 10);
            highScoreEl.textContent = highScore;

            const tick = () => {
                if (isPaused || isGameOver) return;
                const head = { x: snake[0].x + velocityX, y: snake[0].y + velocityY };
                snake.unshift(head);
                if (head.x !== foodX || head.y !== foodY) snake.pop();

                if (head.x < 0 || head.x >= tileCount || head.y < 0 || head.y >= tileCount) {
                    gameOver();
                    return;
                }
                for (let i = 1; i < snake.length; i++) {
                    if (head.x === snake[i].x && head.y === snake[i].y) {
                        gameOver();
                        return;
                    }
                }

                if (head.x === foodX && head.y === foodY) {
                    score += 10;
                    scoreEl.textContent = score;
                    lengthEl.textContent = snake.length;
                    generateFood();
                    if (score > highScore) {
                        highScore = score;
                        highScoreEl.textContent = highScore;
                        localStorage.setItem('snakeHighScore', String(highScore));
                    }
                }

                draw();
            };

            const generateFood = () => {
                foodX = Math.floor(Math.random() * tileCount);
                foodY = Math.floor(Math.random() * tileCount);
                for (const segment of snake) {
                    if (segment.x === foodX && segment.y === foodY) {
                        generateFood();
                        return;
                    }
                }
            };

            const draw = () => {
                ctx.fillStyle = '#1f2937';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.strokeStyle = '#374151';
                ctx.lineWidth = 0.5;
                for (let i = 0; i <= tileCount; i++) {
                    ctx.beginPath(); ctx.moveTo(i * gridSize, 0); ctx.lineTo(i * gridSize, canvas.height); ctx.stroke();
                    ctx.beginPath(); ctx.moveTo(0, i * gridSize); ctx.lineTo(canvas.width, i * gridSize); ctx.stroke();
                }

                ctx.fillStyle = '#ef4444';
                ctx.beginPath();
                ctx.arc(foodX * gridSize + gridSize / 2, foodY * gridSize + gridSize / 2, gridSize / 2 - 2, 0, Math.PI * 2);
                ctx.fill();

                snake.forEach((segment, index) => {
                    ctx.fillStyle = index === 0 ? '#10b981' : `rgba(16, 185, 129, ${1 - (index / snake.length) * 0.5})`;
                    ctx.fillRect(segment.x * gridSize + 1, segment.y * gridSize + 1, gridSize - 2, gridSize - 2);
                });
            };

            const gameOver = () => {
                isGameOver = true;
                clearInterval(gameLoop);
                startBtn.disabled = false;
                startBtnLabel.textContent = '開始遊戲';
                pauseBtn.disabled = true;
                ctx.fillStyle = 'rgba(0,0,0,0.7)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 48px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('遊戲結束!', canvas.width / 2, canvas.height / 2 - 30);
                ctx.font = '24px sans-serif';
                ctx.fillText('分數: ' + score, canvas.width / 2, canvas.height / 2 + 20);
            };

            const startGame = () => {
                if (gameLoop) clearInterval(gameLoop);
                snake = [{ x: 10, y: 10 }];
                velocityX = 1;
                velocityY = 0;
                score = 0;
                isPaused = false;
                isGameOver = false;
                scoreEl.textContent = '0';
                lengthEl.textContent = '1';
                generateFood();
                startBtn.disabled = true;
                startBtnLabel.textContent = '遊戲中…';
                pauseBtn.disabled = false;
                pauseBtnLabel.textContent = '暫停';
                gameLoop = setInterval(tick, gameSpeed);
            };

            startBtn.addEventListener('click', startGame);
            resetBtn.addEventListener('click', startGame);
            pauseBtn.addEventListener('click', () => {
                isPaused = !isPaused;
                if (isPaused) {
                    pauseBtnLabel.textContent = '繼續';
                    clearInterval(gameLoop);
                } else {
                    pauseBtnLabel.textContent = '暫停';
                    gameLoop = setInterval(tick, gameSpeed);
                }
            });

            difficultyBtns.forEach((btn) => {
                btn.addEventListener('click', () => {
                    difficultyBtns.forEach((b) => {
                        b.classList.remove('is-active', 'admin-btn-primary');
                        b.classList.add('admin-btn-outline');
                    });
                    btn.classList.add('is-active', 'admin-btn-primary');
                    btn.classList.remove('admin-btn-outline');
                    gameSpeed = parseInt(btn.dataset.speed || '100', 10);
                    if (gameLoop && !isPaused && !isGameOver) {
                        clearInterval(gameLoop);
                        gameLoop = setInterval(tick, gameSpeed);
                    }
                });
            });

            document.addEventListener('keydown', (event) => {
                if (isGameOver) return;
                const key = event.key;
                if ((key === 'ArrowUp' || key === 'w' || key === 'W') && velocityY !== 1) {
                    velocityX = 0; velocityY = -1; event.preventDefault();
                } else if ((key === 'ArrowDown' || key === 's' || key === 'S') && velocityY !== -1) {
                    velocityX = 0; velocityY = 1; event.preventDefault();
                } else if ((key === 'ArrowLeft' || key === 'a' || key === 'A') && velocityX !== 1) {
                    velocityX = -1; velocityY = 0; event.preventDefault();
                } else if ((key === 'ArrowRight' || key === 'd' || key === 'D') && velocityX !== -1) {
                    velocityX = 1; velocityY = 0; event.preventDefault();
                } else if (key === ' ') {
                    pauseBtn.click();
                    event.preventDefault();
                }
            });

            draw();
        })();
    </script>
@endpush
