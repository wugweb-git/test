(function(){var root=document.getElementById("game");if(!root)return;
  const cells = root.querySelectorAll('[data-cell]');
  let turn = 'X';
  let board = Array(9).fill(null);

  function checkWinner() {
    const wins = [[0,1,2],[3,4,5],[6,7,8],[0,3,6],[1,4,7],[2,5,8],[0,4,8],[2,4,6]];
    for (const [a,b,c] of wins) {
      if (board[a] && board[a] === board[b] && board[a] === board[c]) return board[a];
    }
    return board.every(Boolean) ? 'draw' : null;
  }

  cells.forEach((cell, i) => {
    cell.addEventListener('click', () => {
      if (board[i] || checkWinner()) return;
      board[i] = turn;
      cell.textContent = turn;
      const winner = checkWinner();
      if (winner) {
        setTimeout(() => alert(winner === 'draw' ? "It's a draw!" : `${winner} wins!`), 50);
      }
      turn = turn === 'X' ? 'O' : 'X';
    });
  });
})();
