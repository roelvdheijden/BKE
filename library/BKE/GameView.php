<?PHP
namespace BKE;

class GameView
{
	protected $data; //  holding data

	/* constructor */
	function __construct(GameData $data=null, $output=null)
	{
		$this->data = $data;
		// view output bsed on requested output (default = json)
		switch ($output) {
			case "html" :
				$this->viewHTML();
				break;
			
			default :
				$this->viewJSON();
				break;
		}
	}
	
	protected function viewJSON()
	{
		// build data array
		$dataOutput = array(
			"gameStatus" => $this->data->getGameStatus(),
			"player" => $this->data->getCurrentPlayer()->getName(),
		);
		
		//loop through winLine data and output fields
		
		if($this->data->getGameStatus() == 2) {
			$winFields = $this->data->getWinFields();
			foreach($winFields as $field) {
				$dataOutput['winLine'][] = $field;
			}
		}
		
		// loop through board data (rows and cols)
		$fields = array();
		foreach($this->data->getBoard() as $row => $cols) {
			foreach($cols as $col => $field) {
				// validate field value
				$inactive = false;
				switch ($field) {
					case '0' :
						$field = " ";
						break;
					case '-1' :
						$field = " ";
						$inactive = true;
						break;
				}

				$dataOutput['fields'][] = array("id" => $row.$col, "content" => $field, "inactive" => $inactive);
			}
		}
		
		// actual output to screen
		//header('Content-Type: application/json');
		echo json_encode($dataOutput);
	}
	
	protected function viewHTML()
	{
		// output view as html
	}
	
	
} // end class


?>