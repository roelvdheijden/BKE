<?PHP
namespace Application;

use \Application\Router;
use \BKE\Game;

/* Main Application wrapper - containing application routes */
class Application
{
	protected $router;
	
	/* constructor */
	function __construct()
	{
		session_start();
		session_regenerate_id(true); 
		
		$this->router = new Router();
		// initialize routes
		$this->router->addRoute('//', new Game, 'newGame');
		$this->router->addRoute('/game/new', new Game, 'newGame');
		$this->router->addRoute('/game/play', new Game, 'play');
	}
	
	public function run()
	{
		$this->router->execute();
	}

}
?>