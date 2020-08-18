<?php namespace application;

class Router {
    private array $routes;

    public function load($routes) {
        $this->routes = $routes;
    }

    public function start() {
        $target = $_GET['target'];
        $action = $_GET['action'];
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        $paths = explode('_', $target.'_controller');
        $paths = array_map('ucfirst', $paths);
        if (!isset($this->routes[$target])) {
            return http_response_code(404);
        }
        $controller_name = implode('_', $paths);
        $permitted_actions = $this->permitted_actions($target, $method);
        if (!in_array($action, $permitted_actions)) {
            return http_response_code(405);
        }
        $action_controller = $this->search_action($action, $this->routes[$target][$method]);
        if ($action_controller == null) {
            return http_response_code(401);
        }
        header("Cache-Control: no-cache, must-revalidate");
        $output = call_user_func('\application\controllers\\'.$controller_name.'::'.$action_controller);
        return AUTO_ENCODE_BASE64 ? base64_encode($output) : $output;
    }

    private function permitted_actions($target, $method): array {
        $actions = [];
        $pattern = '/(\w+):[ ]*(\w+)/';
        foreach ($this->routes[$target][$method] as $action) {
            if (preg_match($pattern, $action, $matches)) {
                array_push($actions, $matches[1]);
            } else {
                array_push($actions, $action);
            }
        }
        return $actions;
    }

    private function search_action(string $key, array $actions): ?string {
        $pattern = '/'.$key.':[ ]*(\w+)/';
        foreach ($actions as $action) {
            if (preg_match($pattern, $action, $matches)) {
                return $matches[1];
            } elseif ($action == $key) {
                return $action;
            }
        }
        return null;
    }
}