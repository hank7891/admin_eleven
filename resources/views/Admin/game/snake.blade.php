@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper stitch-page">
        <div class="p-6 lg:p-10 space-y-8">
            {{-- 頁面標題區 --}}
            <div>
                <nav class="flex items-center gap-2 text-[0.75rem] text-outline-variant mb-1 uppercase tracking-widest font-semibold">
                    <a href="{{ asset('admin/') }}" class="hover:text-primary transition-colors">首頁</a>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <span class="text-primary">貪食蛇</span>
                </nav>
                <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">貪食蛇小遊戲</h2>
            </div>

            <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-8">
                <div class="max-w-2xl mx-auto text-center space-y-6">
                    {{-- 遊戲說明 --}}
                    <div class="flex items-center gap-2 p-4 bg-primary/5 border border-primary/10 rounded-xl text-[0.875rem] text-on-surface-variant">
                        <span class="material-symbols-outlined text-primary text-[18px]">info</span>
                        使用方向鍵 ↑ ↓ ← → 或 W A S D 控制蛇的移動，吃到食物可以增加分數和長度。
                    </div>

                    {{-- 遊戲畫布 --}}
                    <canvas id="gameCanvas" width="600" height="600" class="block mx-auto rounded-lg border-2 border-on-surface/10" style="background-color: #1f2937;"></canvas>

                    {{-- 遊戲資訊 --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-emerald-50 rounded-xl p-4">
                            <p class="text-[0.75rem] font-bold text-emerald-600 uppercase">分數</p>
                            <p class="text-[1.5rem] font-bold text-emerald-700" id="score">0</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4">
                            <p class="text-[0.75rem] font-bold text-blue-600 uppercase">長度</p>
                            <p class="text-[1.5rem] font-bold text-blue-700" id="length">3</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-4">
                            <p class="text-[0.75rem] font-bold text-amber-600 uppercase">最高分</p>
                            <p class="text-[1.5rem] font-bold text-amber-700" id="highScore">0</p>
                        </div>
                    </div>

                    {{-- 控制按鈕 --}}
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" id="startBtn" class="px-6 py-2.5 bg-gradient-to-r from-[#667eea] to-[#764ba2] text-white rounded-xl font-bold text-[0.875rem] shadow-lg shadow-indigo-500/20 active:scale-95 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">play_arrow</span> 開始遊戲
                        </button>
                        <button type="button" id="pauseBtn" disabled class="px-6 py-2.5 bg-amber-500 text-white rounded-xl font-bold text-[0.875rem] active:scale-95 transition-all flex items-center gap-2 disabled:opacity-50">
                            <span class="material-symbols-outlined text-[18px]">pause</span> 暫停
                        </button>
                        <button type="button" id="resetBtn" class="px-6 py-2.5 bg-error text-white rounded-xl font-bold text-[0.875rem] active:scale-95 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">refresh</span> 重新開始
                        </button>
                    </div>

                    {{-- 難度選擇 --}}
                    <div class="flex items-center justify-center gap-2">
                        <span class="text-[0.875rem] text-outline font-medium">難度：</span>
                        <button type="button" class="difficulty-btn px-4 py-1.5 rounded-lg text-[0.8125rem] font-semibold border transition-colors" data-speed="150">簡單</button>
                        <button type="button" class="difficulty-btn active px-4 py-1.5 rounded-lg text-[0.8125rem] font-semibold border transition-colors" data-speed="100">普通</button>
                        <button type="button" class="difficulty-btn px-4 py-1.5 rounded-lg text-[0.8125rem] font-semibold border transition-colors" data-speed="60">困難</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .difficulty-btn { border-color: var(--color-outline-variant); color: var(--color-on-surface-variant); }
        .difficulty-btn.active { background: var(--color-primary); color: white; border-color: var(--color-primary); }
    </style>

    <script>
        $(document).ready(function() {
            const canvas = document.getElementById('gameCanvas');
            const ctx = canvas.getContext('2d');
            const gridSize = 20;
            const tileCount = canvas.width / gridSize;

            let snake = [{x: 10, y: 10}];
            let velocityX = 0, velocityY = 0;
            let foodX = 15, foodY = 15;
            let score = 0, gameSpeed = 100, gameLoop = null;
            let isPaused = false, isGameOver = false;
            let highScore = localStorage.getItem('snakeHighScore') || 0;
            $('#highScore').text(highScore);

            function game() {
                if (isPaused || isGameOver) return;
                updateSnake();
                if (checkCollision()) { gameOver(); return; }
                checkFoodCollision();
                clearCanvas();
                drawFood();
                drawSnake();
            }

            function updateSnake() {
                const head = {x: snake[0].x + velocityX, y: snake[0].y + velocityY};
                snake.unshift(head);
                if (head.x !== foodX || head.y !== foodY) snake.pop();
            }

            function checkCollision() {
                const head = snake[0];
                if (head.x < 0 || head.x >= tileCount || head.y < 0 || head.y >= tileCount) return true;
                for (let i = 1; i < snake.length; i++) {
                    if (head.x === snake[i].x && head.y === snake[i].y) return true;
                }
                return false;
            }

            function checkFoodCollision() {
                if (snake[0].x === foodX && snake[0].y === foodY) {
                    score += 10;
                    $('#score').text(score);
                    $('#length').text(snake.length);
                    generateFood();
                    if (score > highScore) {
                        highScore = score;
                        $('#highScore').text(highScore);
                        localStorage.setItem('snakeHighScore', highScore);
                    }
                }
            }

            function generateFood() {
                foodX = Math.floor(Math.random() * tileCount);
                foodY = Math.floor(Math.random() * tileCount);
                for (let segment of snake) {
                    if (segment.x === foodX && segment.y === foodY) { generateFood(); return; }
                }
            }

            function clearCanvas() {
                ctx.fillStyle = '#1f2937';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.strokeStyle = '#374151';
                ctx.lineWidth = 0.5;
                for (let i = 0; i <= tileCount; i++) {
                    ctx.beginPath(); ctx.moveTo(i * gridSize, 0); ctx.lineTo(i * gridSize, canvas.height); ctx.stroke();
                    ctx.beginPath(); ctx.moveTo(0, i * gridSize); ctx.lineTo(canvas.width, i * gridSize); ctx.stroke();
                }
            }

            function drawSnake() {
                snake.forEach((segment, index) => {
                    ctx.fillStyle = index === 0 ? '#10b981' : `rgba(16, 185, 129, ${1 - (index / snake.length) * 0.5})`;
                    ctx.fillRect(segment.x * gridSize + 1, segment.y * gridSize + 1, gridSize - 2, gridSize - 2);
                    if (index === 0) {
                        ctx.fillStyle = '#ffffff';
                        const s = 3;
                        if (velocityX === 1) { ctx.fillRect(segment.x*gridSize+14, segment.y*gridSize+5, s, s); ctx.fillRect(segment.x*gridSize+14, segment.y*gridSize+12, s, s); }
                        else if (velocityX === -1) { ctx.fillRect(segment.x*gridSize+3, segment.y*gridSize+5, s, s); ctx.fillRect(segment.x*gridSize+3, segment.y*gridSize+12, s, s); }
                        else if (velocityY === 1) { ctx.fillRect(segment.x*gridSize+5, segment.y*gridSize+14, s, s); ctx.fillRect(segment.x*gridSize+12, segment.y*gridSize+14, s, s); }
                        else if (velocityY === -1) { ctx.fillRect(segment.x*gridSize+5, segment.y*gridSize+3, s, s); ctx.fillRect(segment.x*gridSize+12, segment.y*gridSize+3, s, s); }
                    }
                });
            }

            function drawFood() {
                ctx.fillStyle = '#ef4444';
                ctx.beginPath();
                ctx.arc(foodX * gridSize + gridSize/2, foodY * gridSize + gridSize/2, gridSize/2 - 2, 0, 2 * Math.PI);
                ctx.fill();
            }

            function gameOver() {
                isGameOver = true;
                clearInterval(gameLoop);
                $('#startBtn').prop('disabled', false).html('<span class="material-symbols-outlined text-[18px]">play_arrow</span> 開始遊戲');
                $('#pauseBtn').prop('disabled', true);
                ctx.fillStyle = 'rgba(0,0,0,0.7)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 48px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('遊戲結束!', canvas.width/2, canvas.height/2 - 30);
                ctx.font = '24px Arial';
                ctx.fillText('分數: ' + score, canvas.width/2, canvas.height/2 + 20);
            }

            function startGame() {
                if (gameLoop) clearInterval(gameLoop);
                snake = [{x: 10, y: 10}];
                velocityX = 1; velocityY = 0;
                score = 0; isPaused = false; isGameOver = false;
                $('#score').text(score); $('#length').text(snake.length);
                generateFood();
                $('#startBtn').prop('disabled', true).html('<span class="material-symbols-outlined text-[18px]">play_arrow</span> 遊戲中...');
                $('#pauseBtn').prop('disabled', false);
                gameLoop = setInterval(game, gameSpeed);
            }

            $(document).keydown(function(e) {
                if (isGameOver) return;
                switch(e.key) {
                    case 'ArrowUp': case 'w': case 'W': if (velocityY !== 1) { velocityX = 0; velocityY = -1; } e.preventDefault(); break;
                    case 'ArrowDown': case 's': case 'S': if (velocityY !== -1) { velocityX = 0; velocityY = 1; } e.preventDefault(); break;
                    case 'ArrowLeft': case 'a': case 'A': if (velocityX !== 1) { velocityX = -1; velocityY = 0; } e.preventDefault(); break;
                    case 'ArrowRight': case 'd': case 'D': if (velocityX !== -1) { velocityX = 1; velocityY = 0; } e.preventDefault(); break;
                    case ' ': $('#pauseBtn').click(); e.preventDefault(); break;
                }
            });

            $('#startBtn').click(startGame);
            $('#pauseBtn').click(function() {
                isPaused = !isPaused;
                if (isPaused) { $(this).html('<span class="material-symbols-outlined text-[18px]">play_arrow</span> 繼續'); clearInterval(gameLoop); }
                else { $(this).html('<span class="material-symbols-outlined text-[18px]">pause</span> 暫停'); gameLoop = setInterval(game, gameSpeed); }
            });
            $('#resetBtn').click(startGame);
            $('.difficulty-btn').click(function() {
                $('.difficulty-btn').removeClass('active');
                $(this).addClass('active');
                gameSpeed = parseInt($(this).data('speed'));
                if (gameLoop && !isPaused) { clearInterval(gameLoop); gameLoop = setInterval(game, gameSpeed); }
            });

            clearCanvas(); drawFood(); drawSnake();
        });
    </script>
@endsection
