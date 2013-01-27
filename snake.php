<html>
    <meta charset="UTF-8">
    <head>
        <link rel="stylesheet" type="text/css" href="mystyle.css">
        <title>Snake the game</title>
        <script>
            var boardSize = <?php echo $_GET["bsize"]; ?>;
            var pieceSize = 20;
            var stop = false; // debug flag
            var maxFood = <?php echo $_GET["foods"]; ?>;
            var speed = <?php echo $_GET["speed"]; ?>;
            function SnakeBody() {  // same class for snake head & snake body.
                this.x = -1;
                this.y = -1;
            };
            
            SnakeBody.prototype.setPos = function(newX, newY) {
                this.x = newX;
                this.y = newY;
            };
            
            SnakeBody.prototype.move = function(dir) {
                switch (dir) {
                    case 'l':
                        this.x -= 1;
                        break;
                    case 'r':
                        this.x += 1;
                        break;
                    case 'u':
                        this.y -= 1;
                        break;
                    case 'd':
                        this.y += 1;
                        break;
                }
            };
            
            function Board() {
                this.board = new Array(boardSize);
                for (var i = 0; i < boardSize; i++) {
                    this.board[i] = new Array(boardSize);
                    for (var j = 0; j < boardSize; j++) {
                        this.board[i][j] = ' ';
                    }
                }
                
                for (var i = 0; i < boardSize; i++) {
                    this.board[0][i] = 'w';
                    this.board[boardSize - 1][i] = 'w';
                    this.board[i][0] = 'w';
                    this.board[i][boardSize - 1] = 'w'
                }
                
                //this.board[15][10] = 'f';
                this.foodCnt = 0;
                
                this.snakeDir = 'r';
                
                this.snake = new Array();
                this.snakeLength = 1;
                
                this.snake[0] = new SnakeBody();    // 0 is always the head
                
                this.snake[0].setPos((boardSize/2)>>0, (boardSize/2)>>0);   // start from the center
                //this.snake[0].setPos(0,0);
                
                this.addSnakeToBoard();
                this.drawAll();
            };
            
            Board.prototype.addSnakeToBoard = function() {
                // first clear the board;
                for (var i = 0; i < boardSize; i++) {
                    for (var j = 0; j < boardSize; j++) {
                        if (this.board[i][j] == 's') {
                            this.board[i][j] = ' ';
                        }
                    }
                }
                for (var i = this.snakeLength - 1; i >= 0; i--) {
                    var xCord = this.snake[i].x;
                    var yCord = this.snake[i].y;
                    var cellStt = this.board[xCord][yCord];
                    if (xCord < 0 || yCord < 0) {
                        continue;
                    }
                    if (cellStt == ' ') {
                        this.board[xCord][yCord] = 's';
                    }else{
                        this.board[xCord][yCord] = ' '; 
                        // clear out the cell because if it colide and die, colide() will not return
                        // otherwise it colide with food and food is consumed.
                        this.colide(cellStt);
                        return;
                    }
                }
                if (this.foodCnt < maxFood) {
                    var foodX, foodY, foodCell;
                    do {
                        foodX = Math.floor(Math.random()*boardSize);
                        foodY = Math.floor(Math.random()*boardSize);
                        foodCell = this.board[foodX][foodY];
                    } while (foodCell != ' ');
                    this.board[foodX][foodY] = 'f';
                    this.foodCnt++;
                }
            };
            
            Board.prototype.snakeMove = function() {
                var oldX = this.snake[0].x;
                var oldY = this.snake[0].y;
                this.snake[0].move(this.snakeDir);
                for (var i = 1; i < this.snakeLength; i++){
                    var tempX = this.snake[i].x;
                    var tempY = this.snake[i].y;
                    this.snake[i].setPos(oldX, oldY);
                    oldX = tempX;
                    oldY = tempY;
                }
            };
            
            Board.prototype.step = function() {
                if (stop == false) {
                    this.snakeMove();
                    this.addSnakeToBoard();
                    this.drawAll();
                }
            };
            
            Board.prototype.colide = function(arg) {
                switch(arg) {
                    case 's':
                    case 'w':
                        window.location = "gameover.php?score=" + (this.snakeLength - 1);
                        stop = true;
                        break;
                    case 'f':
                        this.snake[this.snakeLength] = new SnakeBody();
                        this.snakeLength++;
                        //this.addSnakeToBoard();
                        this.foodCnt--;
                        updateScore(this.snakeLength - 1);
                        break;
                }
            }
            
            Board.prototype.drawAll = function() {
                clearGameArea();
                for (var i = 0; i < boardSize; i++) {
                    for (var j = 0; j < boardSize; j++) {
                        if (this.board[i][j] == 'w'){
                            var line = '<div style="position: absolute; left:'+(i*pieceSize)+'px; top:'+(j*pieceSize)+'px;">';
                            line += '<img src="wall.jpg" /></div>';
                            document.getElementById('game-area').innerHTML += line;
                            }
                        if (this.board[i][j] == 's'){
                            var line = '<div style="position: absolute; left:'+(i*pieceSize)+'px; top:'+(j*pieceSize)+'px;">';
                            line += '<img src="snake.jpg" /></div>';
                            document.getElementById('game-area').innerHTML += line;
                            }
                        if (this.board[i][j] == 'f'){
                            var line = '<div style="position: absolute; left:'+(i*pieceSize)+'px; top:'+(j*pieceSize)+'px;">';
                            line += '<img src="food.jpg" /></div>';
                            document.getElementById('game-area').innerHTML += line;
                            }
                        }
                    }
                };
                
                function clearGameArea() {
                    document.getElementById('game-area').innerHTML = '';
                };
                function updateScore(src) {
                    document.getElementById('score-area').innerHTML = 'score: ' + src;
                }
        </script>
    </head>
    <body>
        <div id="game-area" tabindex="0">
        </div>
        <div id="score-area" class="rightDiv">
        score: 0
        </div>
        <script type="text/javascript">
                var gameBoard = new Board();
                setInterval('gameBoard.step()', speed);
                window.addEventListener('keydown', function(event) {
                switch (event.keyCode) {
                    case 37: // Left
                        if (gameBoard.snakeDir != 'r'){
                            gameBoard.snakeDir = 'l';
                        }
                        break;
                    case 38: // Up
                        if (gameBoard.snakeDir != 'd'){
                            gameBoard.snakeDir = 'u';
                        }
                        break;
                    case 39: // Right
                        if (gameBoard.snakeDir != 'l'){
                            gameBoard.snakeDir = 'r';
                        }
                        break;
                    case 40: // Down
                        if(gameBoard.snakeDir != 'u'){
                            gameBoard.snakeDir = 'd';
                        }
                        break;
                }
            }, false);
        </script>
        <div id="debug">
        </div>
    </body>
</html>