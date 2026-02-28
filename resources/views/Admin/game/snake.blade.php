@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $pageTitle ?? '貪食蛇小遊戲' }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?= asset('admin/') ?>">Home</a></li>
                            <li class="breadcrumb-item active">貪食蛇</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-gamepad mr-1"></i>
                                    貪食蛇遊戲
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-md-8 text-center">
                                        <!-- 遊戲說明 -->
                                        <div class="alert alert-info">
                                            <h5><i class="icon fas fa-info"></i> 遊戲說明</h5>
                                            使用方向鍵 <kbd>↑</kbd> <kbd>↓</kbd> <kbd>←</kbd> <kbd>→</kbd> 或 <kbd>W</kbd> <kbd>A</kbd> <kbd>S</kbd> <kbd>D</kbd> 控制蛇的移動，吃到食物可以增加分數和長度。
                                        </div>

                                        <!-- 遊戲畫布 -->
                                        <canvas id="gameCanvas" width="600" height="600" style="border: 2px solid #343a40; background-color: #1f2937; border-radius: 4px;"></canvas>

                                        <!-- 遊戲資訊 -->
                                        <div class="row mt-3">
                                            <div class="col-md-4">
                                                <div class="info-box bg-success">
                                                    <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">分數</span>
                                                        <span class="info-box-number" id="score">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="fas fa-ruler"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">長度</span>
                                                        <span class="info-box-number" id="length">3</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box bg-warning">
                                                    <span class="info-box-icon"><i class="fas fa-crown"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">最高分</span>
                                                        <span class="info-box-number" id="highScore">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 遊戲控制按鈕 -->
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-primary btn-lg" id="startBtn">
                                                <i class="fas fa-play"></i> 開始遊戲
                                            </button>
                                            <button type="button" class="btn btn-warning btn-lg" id="pauseBtn" disabled>
                                                <i class="fas fa-pause"></i> 暫停
                                            </button>
                                            <button type="button" class="btn btn-danger btn-lg" id="resetBtn">
                                                <i class="fas fa-redo"></i> 重新開始
                                            </button>
                                        </div>

                                        <!-- 難度選擇 -->
                                        <div class="mt-3">
                                            <label>遊戲難度：</label>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-outline-success difficulty-btn" data-speed="150">簡單</button>
                                                <button type="button" class="btn btn-outline-warning difficulty-btn active" data-speed="100">普通</button>
                                                <button type="button" class="btn btn-outline-danger difficulty-btn" data-speed="60">困難</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>

    <style>
        #gameCanvas {
            display: block;
            margin: 20px auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .difficulty-btn.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
    </style>

    <script>
        $(document).ready(function() {
            const canvas = document.getElementById('gameCanvas');
            const ctx = canvas.getContext('2d');
            const gridSize = 20;
            const tileCount = canvas.width / gridSize;

            let snake = [{x: 10, y: 10}];
            let velocityX = 0;
            let velocityY = 0;
            let foodX = 15;
            let foodY = 15;
            let score = 0;
            let gameSpeed = 100;
            let gameLoop = null;
            let isPaused = false;
            let isGameOver = false;
            let highScore = localStorage.getItem('snakeHighScore') || 0;

            // 初始化最高分顯示
            $('#highScore').text(highScore);

            // 遊戲主循環
            function game() {
                if (isPaused || isGameOver) return;

                updateSnake();
                if (checkCollision()) {
                    gameOver();
                    return;
                }
                checkFoodCollision();
                clearCanvas();
                drawFood();
                drawSnake();
            }

            // 更新蛇的位置
            function updateSnake() {
                const head = {x: snake[0].x + velocityX, y: snake[0].y + velocityY};
                snake.unshift(head);
                if (head.x !== foodX || head.y !== foodY) {
                    snake.pop();
                }
            }

            // 檢查碰撞
            function checkCollision() {
                const head = snake[0];
                // 撞牆
                if (head.x < 0 || head.x >= tileCount || head.y < 0 || head.y >= tileCount) {
                    return true;
                }
                // 撞自己
                for (let i = 1; i < snake.length; i++) {
                    if (head.x === snake[i].x && head.y === snake[i].y) {
                        return true;
                    }
                }
                return false;
            }

            // 檢查是否吃到食物
            function checkFoodCollision() {
                if (snake[0].x === foodX && snake[0].y === foodY) {
                    score += 10;
                    $('#score').text(score);
                    $('#length').text(snake.length);
                    generateFood();

                    // 更新最高分
                    if (score > highScore) {
                        highScore = score;
                        $('#highScore').text(highScore);
                        localStorage.setItem('snakeHighScore', highScore);
                    }
                }
            }

            // 生成食物
            function generateFood() {
                foodX = Math.floor(Math.random() * tileCount);
                foodY = Math.floor(Math.random() * tileCount);
                // 確保食物不會生成在蛇身上
                for (let segment of snake) {
                    if (segment.x === foodX && segment.y === foodY) {
                        generateFood();
                        return;
                    }
                }
            }

            // 清除畫布
            function clearCanvas() {
                ctx.fillStyle = '#1f2937';
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                // 繪製網格線
                ctx.strokeStyle = '#374151';
                ctx.lineWidth = 0.5;
                for (let i = 0; i <= tileCount; i++) {
                    ctx.beginPath();
                    ctx.moveTo(i * gridSize, 0);
                    ctx.lineTo(i * gridSize, canvas.height);
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.moveTo(0, i * gridSize);
                    ctx.lineTo(canvas.width, i * gridSize);
                    ctx.stroke();
                }
            }

            // 繪製蛇
            function drawSnake() {
                snake.forEach((segment, index) => {
                    if (index === 0) {
                        // 蛇頭 - 綠色
                        ctx.fillStyle = '#10b981';
                    } else {
                        // 蛇身 - 漸變綠色
                        const opacity = 1 - (index / snake.length) * 0.5;
                        ctx.fillStyle = `rgba(16, 185, 129, ${opacity})`;
                    }
                    ctx.fillRect(segment.x * gridSize + 1, segment.y * gridSize + 1, gridSize - 2, gridSize - 2);

                    // 蛇頭加上眼睛
                    if (index === 0) {
                        ctx.fillStyle = '#ffffff';
                        const eyeSize = 3;
                        if (velocityX === 1) { // 向右
                            ctx.fillRect(segment.x * gridSize + 14, segment.y * gridSize + 5, eyeSize, eyeSize);
                            ctx.fillRect(segment.x * gridSize + 14, segment.y * gridSize + 12, eyeSize, eyeSize);
                        } else if (velocityX === -1) { // 向左
                            ctx.fillRect(segment.x * gridSize + 3, segment.y * gridSize + 5, eyeSize, eyeSize);
                            ctx.fillRect(segment.x * gridSize + 3, segment.y * gridSize + 12, eyeSize, eyeSize);
                        } else if (velocityY === 1) { // 向下
                            ctx.fillRect(segment.x * gridSize + 5, segment.y * gridSize + 14, eyeSize, eyeSize);
                            ctx.fillRect(segment.x * gridSize + 12, segment.y * gridSize + 14, eyeSize, eyeSize);
                        } else if (velocityY === -1) { // 向上
                            ctx.fillRect(segment.x * gridSize + 5, segment.y * gridSize + 3, eyeSize, eyeSize);
                            ctx.fillRect(segment.x * gridSize + 12, segment.y * gridSize + 3, eyeSize, eyeSize);
                        }
                    }
                });
            }

            // 繪製食物
            function drawFood() {
                ctx.fillStyle = '#ef4444';
                ctx.beginPath();
                ctx.arc(foodX * gridSize + gridSize / 2, foodY * gridSize + gridSize / 2, gridSize / 2 - 2, 0, 2 * Math.PI);
                ctx.fill();
            }

            // 遊戲結束
            function gameOver() {
                isGameOver = true;
                clearInterval(gameLoop);
                $('#startBtn').prop('disabled', false).html('<i class="fas fa-play"></i> 開始遊戲');
                $('#pauseBtn').prop('disabled', true);

                // 顯示遊戲結束訊息
                ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 48px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('遊戲結束!', canvas.width / 2, canvas.height / 2 - 30);
                ctx.font = '24px Arial';
                ctx.fillText('分數: ' + score, canvas.width / 2, canvas.height / 2 + 20);

                if (score >= highScore) {
                    ctx.fillStyle = '#fbbf24';
                    ctx.fillText('🎉 新紀錄！🎉', canvas.width / 2, canvas.height / 2 + 60);
                }
            }

            // 開始遊戲
            function startGame() {
                if (gameLoop) clearInterval(gameLoop);

                // 重置遊戲狀態
                snake = [{x: 10, y: 10}];
                velocityX = 1;
                velocityY = 0;
                score = 0;
                isPaused = false;
                isGameOver = false;
                $('#score').text(score);
                $('#length').text(snake.length);
                generateFood();

                // 禁用開始按鈕，啟用暫停按鈕
                $('#startBtn').prop('disabled', true).html('<i class="fas fa-play"></i> 遊戲中...');
                $('#pauseBtn').prop('disabled', false);

                gameLoop = setInterval(game, gameSpeed);
            }

            // 鍵盤控制
            $(document).keydown(function(e) {
                if (isGameOver) return;

                switch(e.key) {
                    case 'ArrowUp':
                    case 'w':
                    case 'W':
                        if (velocityY !== 1) { velocityX = 0; velocityY = -1; }
                        e.preventDefault();
                        break;
                    case 'ArrowDown':
                    case 's':
                    case 'S':
                        if (velocityY !== -1) { velocityX = 0; velocityY = 1; }
                        e.preventDefault();
                        break;
                    case 'ArrowLeft':
                    case 'a':
                    case 'A':
                        if (velocityX !== 1) { velocityX = -1; velocityY = 0; }
                        e.preventDefault();
                        break;
                    case 'ArrowRight':
                    case 'd':
                    case 'D':
                        if (velocityX !== -1) { velocityX = 1; velocityY = 0; }
                        e.preventDefault();
                        break;
                    case ' ':
                        $('#pauseBtn').click();
                        e.preventDefault();
                        break;
                }
            });

            // 按鈕事件
            $('#startBtn').click(function() {
                startGame();
            });

            $('#pauseBtn').click(function() {
                isPaused = !isPaused;
                if (isPaused) {
                    $(this).html('<i class="fas fa-play"></i> 繼續');
                    clearInterval(gameLoop);
                } else {
                    $(this).html('<i class="fas fa-pause"></i> 暫停');
                    gameLoop = setInterval(game, gameSpeed);
                }
            });

            $('#resetBtn').click(function() {
                startGame();
            });

            // 難度選擇
            $('.difficulty-btn').click(function() {
                $('.difficulty-btn').removeClass('active');
                $(this).addClass('active');
                gameSpeed = parseInt($(this).data('speed'));

                // 如果遊戲正在進行，重新設定速度
                if (gameLoop && !isPaused) {
                    clearInterval(gameLoop);
                    gameLoop = setInterval(game, gameSpeed);
                }
            });

            // 初始化畫布
            clearCanvas();
            drawFood();
            drawSnake();
        });
    </script>
@endsection

