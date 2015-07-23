<?PHP
namespace BKE;
use \BKE\GameData;

/* Game Controller Class - process user input to control the game model (in session) */
class Game
{
	protected $gameData; // model holding data
	
	/* constructor */
	function __construct()
	{
		// get gameData object from session
		if (isset($_SESSION['GameData']) && $_SESSION['GameData'] instanceof GameData)
    	$this->gameData = &$_SESSION['GameData'];
	}
	
	/* public routing method calls */
	function newGame(array $params=null)
	{
		// end session
		unset($_SESSION['GameData']);
		session_unset();
		session_destroy();
		// start new session
		session_start();
		session_regenerate_id(true); 
		
		
		$_SESSION['GameData'] = new GameData();
		$this->gameData = &$_SESSION['GameData'];
		
		$this->gameData->newGame();
		$this->gameData->playMove(2,2,$this->gameData->getCurrentPlayer()); // fixed first move
		$this->gameData->view('json');
	}
	
	function play(array $params=null)
	{
		// validate input parameter and naming
		if( isset($params[0]) &&
				is_array($param = explode('_', $params[0])) &&
					$param[0] == "field") {

			$row = (int) $param[1][0];
			$col = (int) $param[1][1];

			// change data through model method 
			$this->gameData->playMove($row, $col, $this->gameData->getCurrentPlayer());
		}
		
		// update view through model
		$this->gameData->view('json');
	}
} // end class
?>