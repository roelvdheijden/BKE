<?PHP
namespace BKE;

class Board
{
	protected $rows = 3;
	protected $cols = 3;
	protected $grid;
	
	/* constructor */
	public function __construct($rows=null, $cols=null)
	{
		// set given rows and cols
		if($rows != null)
			$this->rows = $rows;
			
		if($cols != null)
			$this->cols = $cols;
		
		// initialize grid and fields
		$this->fields = array();
		
		// loop through rows
		for ($r = 0; $r < $this->rows; $r++) {
			// initialize row
			$this->grid[$r] = array();
			
			// loop through cols
			for ($c = 0; $c < $this->cols; $c++) {
				// initialize field
				$this->grid[$r][$c] = 0;
			}
		}
	}
	
	/* get all fields in the grid */
	public function getBoard()
	{
		return $this->grid;
	}
	/* set all fields in the grid */
	public function setBoard($board=null)
	{
		if( is_array($board) )
			return $this->grid = $board;
		
		return false;
	}
	
	/* get a field from the grid */
	public function getField($row=null, $col=null)
	{
		if( isset($this->grid[$row][$col]) )
			return $this->grid[$row][$col];
		
		return false;
	}
	/* set a field on the grid */
	public function setField($row=null, $col=null, $symbol=null)
	{
		if( isset($this->grid[$row][$col]) )
			return $this->grid[$row][$col] = $symbol;
		
		return false;
	}

	/* set an entire row in the grid with the same symbol */
	public function setRow($row=null, $symbol=null)
	{
		for($col=0; $col<$this->getCols(); $col++)
			$this->setField($row, $col, $symbol);
	}
	/* set an entire col in the grid with the same symbol */
	public function setCol($col=null, $symbol=null)
	{
		for($row=0; $row<$this->getRows(); $row++)
			$this->setField($row, $col, $symbol);
	}
	
	/* get number of row / cols */
	public function getRows()
	{
		return $this->rows;
	}
	public function getCols()
	{
		return $this->cols;
	}

} // end class

?>