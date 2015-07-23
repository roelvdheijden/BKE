<?PHP
namespace BKE;
use \BKE\Board;

class BKEBoard extends Board
{
	/* constructor */
	public function __construct()
	{
		// initialize grid
		parent::__construct(3, 3);
	}
	
	public function totalFields()
	{
		return $this->getRows() * $this->getCols();
	}

	/* find winning condition for a symbol */
	public function hasWinningCondition($symbol=null)
	{
		// validate symbol input
		if($symbol !== null) {
			// use private helperfunctions to find matches
			$win = $this->hasSymbolInRow($symbol);
			if($win !== false)
				return $win;
			
			$win = $this->hasSymbolInCol($symbol);
			if($win !== false)
				return $win;
			
			$win = $this->hasSymbolInDiag($symbol);
			if($win !== false)
				return $win;
		}
		
		return false;	
	}


 /* private helper functions */
	private function hasSymbolInRow($symbol=null)
	{
		// validate symbol input
		if($symbol === null)
			return false;
		
		// loop through rows to find matches
		for($r=0; $r < $this->getRows(); $r++) {
			$match = array();
			for($c=0; $c < $this->getCols(); $c++) {
				if( $this->getField($r,$c) === $symbol)
					$match[] = $r.$c;
			}
			// return true if matches equals number of cols in the row
			if( count($match) === $this->getCols())
				return $match;
		}	

		return false;
	}
	
	private function hasSymbolInCol($symbol=null)
	{
	// validate symbol input
		if($symbol === null)
			return false;
		
		// loop through cols to find matches
		for($c=0; $c < $this->getCols(); $c++) {
			$match = array();
			for($r=0; $r < $this->getRows(); $r++) {
				if( $this->getField($r,$c) === $symbol)
					$match[] = $r.$c;
			}
			
			// return true if matches equals number of rows in the col
			if( count($match) === $this->getRows())
				return $match;
		}	

		return false;
	}
	
	private function hasSymbolInDiag($symbol=null)
	{
		// validate symbol input
		if($symbol === null)
			return false;
		
		$match1 = $match2 =array();
		// loop through rows to find matches
		for($r=0; $r < $this->getRows(); $r++) {
			if( $this->getField($r,$r) === $symbol)
				$match1[] = $r.$r;
			if( $this->getField($r,$this->getCols()-1-$r) === $symbol)
				$match2[] = $r.($this->getCols()-1-$r);
		}
			
		// return true if matches equals number of cols in a row
		if( count($match1) === $this->getCols())
			return $match1;
		if( count($match2) === $this->getCols())
			return $match2;
				
		return false;
	}
	
	
	
} // end class
?>