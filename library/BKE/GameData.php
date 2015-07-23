<?PHP
namespace BKE;
use \BKE\Board;
use \BKE\BKEBoard;
use \BKE\GameView;

/* Game model class - store gamedata and update view */
class GameData
{
	protected $board;							// classic game board
	protected $gameboard;					// the extended gameboard (5x5)
	protected $gameBoardOffset;		// board offset in rows and cols
	
	protected $players;						// players
	protected $moves;							// move history
	protected $winFields;					// winning fields
	protected $gameStatus; 				// gamestatus: 0-no game; 1-running game; 2-game won; 3-game draw;
	
	/* constructor */
	public function __construct()
	{
		$this->newGame(); 
	}
	
	/* initialize properties for a new game - called by controller */
	public function newGame()
	{
		$this->board = new BKEBoard(); 
		$this->gameboard = new Board(5,5); 
		$this->gameBoardOffset = array(0,0); 
		
		$this->players = new Players();
		$this->players->setPlayer("Player 1", "X");
		$this->players->setPlayer("Player 2", "O");
		
		$this->setGameStatus(1); // set gamestatus to running
		$this->moves = array();
	}
	
	/* play a move - called by controller */
	public function playMove( $row=null, $col=null, Player $player=null)
	{
		
		// validate gamestatus and active player, otherwise start new game first
		if( $this->getGameStatus() != 1 || $player === null) {
			$this->newGame();
		}
		
		// validate input variables on value, type and check if gameboard field is available
		if( $row !== null && is_int($row) &&
		 		$col !== null && is_int($col) &&
		 		$this->gameboard->getField($row, $col) === 0 )
		{

			/* extended gameboard logic */
			// set field on the extended gameboard
			$this->gameboard->setField($row, $col, $player->getSymbol());
			
			// deterime row/col offset from center
			$rowOffset = $row - (($this->gameboard->getRows()-1) / 2);
			$colOffset = $col - (($this->gameboard->getRows()-1) / 2);

			// set gameboardOffset
			$this->gameBoardOffset[0] = max($this->gameBoardOffset[0], $rowOffset);
			$this->gameBoardOffset[1] =  max($this->gameBoardOffset[1], $colOffset);
			
			// limit extended gameboard
			$this->playExtendedLogic($rowOffset, $colOffset);

			// store move in moves-array
			$this->moves[] = array($row, $col);

			/*classic board logic */
			$this->playClassicLogic();
			
		} else {
				// invalid input or field
				// do nothing for now
		}
	}
	
	/* private helper function for playMove() */
	
	// play extended game logic, limit gameboard
	protected function playExtendedLogic($rowOffset=null, $colOffset=null)
	{
		// disable row(s) based on offset
		if ($rowOffset >= 1)
			$this->gameboard->setRow(0, -1);
		if ($rowOffset == 2)
			$this->gameboard->setRow(1, -1);
		if ($rowOffset == -2)
			$this->gameboard->setRow(3, -1);		
		if ($rowOffset <= -1)
			$this->gameboard->setRow(4, -1);		
		
		// disable col(s) based on offset
		if ($colOffset >= 1)
			$this->gameboard->setCol(0, -1);
		if ($colOffset == 2)
			$this->gameboard->setCol(1, -1);
		if ($colOffset == -2)
			$this->gameboard->setCol(3, -1);		
		if ($colOffset <= -1)
			$this->gameboard->setCol(4, -1);		
	}
	
	// play classic BKE game logic
	protected function playClassicLogic()
	{
		// get classic gameboard from extended board
		$grid = array_slice($this->gameboard->getBoard(), $this->gameBoardOffset[0], $this->board->getRows() );
		for($r=0; $r<count($grid); $r++)
			$grid[$r] = array_slice($grid[$r], $this->gameBoardOffset[1], $this->board->getCols() );
		
		// set classic board
		$this->board->setBoard($grid);
		
		// update game status and set next player
		$this->updateGameStatus();
		$this->nextPlayer();
	}
	// test for win conditions and update gameStatus
	protected function updateGameStatus()
	{
		// check for winning move after a min number of moves
		if(count($this->moves) >= 5) {
			// get winning fields
			$this->winFields = $this->board->hasWinningCondition($this->getCurrentPlayer()->getSymbol());
			if ( $this->winFields !== false ) {
				$this->setGameStatus(2);  // set gamestatus to a win
			} else if(count($this->moves) >= $this->board->totalFields()) {
				$this->setGameStatus(3); // set gamestatus to a draw
			}
		}
	}
	// set next Player
	protected function nextPlayer()
	{
		// set next player if game is has not ended
		if ($this->getGameStatus() == 1)
			$this->players->nextPlayer();
	}

	/* update view and show output (json or html; default = json) - called by controller */
	public function view( $output=null ) {
		if ($output != "json" && $output != "html")
			$output = "json"; //set default output if none or invalid is given
		
		// create view	
		new GameView($this, $output);
	}
	
	/* helper get/set-functions (for view and controller) */
	public function getBoard() {
		return $this->gameboard->getBoard();
	}
	public function getWinFields() {
		// convert returned winning fields to extended gameboard field (adding boardOffset)
		foreach($this->winFields as $field) {
			$dataOutput[] = $field[0]+$this->gameBoardOffset[0] . $field[1]+$this->gameBoardOffset[1];
		}
		return $dataOutput;
	}
	public function getGameStatus() {
		return $this->gameStatus;
	}
	public function setGameStatus( $status=null ) {
		if ( $status !== null && is_int($status) ) {
			$this->gameStatus = $status;
			return $this->gameStatus;
		}
		return false;
	}
	public function getCurrentPlayer() {
		return $this->players->getCurrentPlayer();
	}
	
} // end class



/* Players Data Object */
Class Players
{
	protected $players;
	protected $currentPlayer;
	
	/* constructor */
	public function __construct()
	{	}
	
	public function setPlayer($name=null, $symbol=null)
	{
		$this->players[] = new Player($name, $symbol);
		$this->currentPlayer = reset($this->players);
	}
	public function nextPlayer()
	{
		$currentkey = array_search($this->currentPlayer, $this->players, true);
		$this->currentPlayer = (isset($this->players[$currentkey+1])) ? $this->players[$currentkey+1] : reset($this->players);
	}
	public function getCurrentPlayer()
	{
		return $this->currentPlayer;
	}
}

/* Player Data Object */
Class Player
{
	protected $name;
	protected $symbol;
	
	/* constructor */
	public function __construct($name=null, $symbol=null)
	{
		if($name !== null)
			$this->name = $name;	
		
		if($symbol !== null)
			$this->symbol = $symbol;	
	}
	
	/* get functions */
	public function getName()
	{
		return $this->name;
	}
	public function getSymbol()
	{
		return $this->symbol;
	}
}
?>