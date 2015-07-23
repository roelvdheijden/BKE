<?PHP
namespace Application;

/* Simplified Router : directing url request to propriorate class and method */
class Router
{
	public $routes;
	
	/* constructor */
	function __construct()
	{
	}
	
	/* add route */
	public function addRoute($route=null, $controller=null, $method=null)
	{
		if($route !== null && $controller !== null && $method !== null)
			$this->routes[$route] = new Route($route, $controller, $method);
	}

	/* execute route */
	public function execute()
	{
		/* explode server-pathinfo to array and parse it */
		$params = explode("/", ( isset($_SERVER['PATH_INFO']) ) ? $_SERVER['PATH_INFO'] : '/');
		array_shift($params); // remove first empty element
		$path = '/'.array_shift($params); // get controller from array
		$path .= '/'.array_shift($params); // get method from array

		// execute route if the method exists in controller
		if( isset($this->routes[$path]) &&
					method_exists ($this->routes[$path]->controller, $this->routes[$path]->method)
			) {
				$this->routes[$path]->controller->{$this->routes[$path]->method}($params);
		} else {
			// extened error handling if route does not exist
			// returning homepage for now
			$path = "//";
			$this->routes[$path]->controller->{$this->routes[$path]->method}($params);
		}
	}
}

/* Route Data Object */
class Route {
	public $path;
	public $controller;
	public $method;
	
	/* constructor */
	public function __construct($path=null, $controller=null, $method=null)
	{
		if($path !== null)
			$this->path = $path;
		
		if($controller !== null)
			$this->controller = $controller;	
		
		if($method !== null)
			$this->method = $method;	
	}
}

?>