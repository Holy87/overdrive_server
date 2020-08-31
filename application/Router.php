<?php namespace application;

class Router {
    private array $routes;
    private const IMAGE_FILE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

    public function load($routes) {
        $this->routes = $routes;
    }

    public function start() {
        $target = strtolower($_GET['target']);
        $action = strtolower($_GET['action']);
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $res_type = $this->get_resource_type($action);
        $action = $this->get_real_action($action);

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
        $output = call_user_func('\application\controllers\\'.$controller_name.'::'.$action_controller);
        if (!$this->is_image_requested($res_type)) {
            return $this->process_data($res_type, $output);
        } else {
            return $this->process_image($res_type, $output);
        }
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

    private function get_resource_type(string $action): ?string {
        $data = explode('.', $action);
        if (isset($data[1])) {
            $_GET['file_type'] = $data[1];
            return $data[1];
        } else {
            return null;
        }
    }

    private function get_real_action(string $action): string {
        return explode('.', $action)[0];
    }

    private function is_image_requested(?string $request_type): bool {
        if ($request_type == null) return false;
        return in_array($request_type, self::IMAGE_FILE_TYPES);
    }

    private function process_data(?string $resource_type, $data) {
        header("Cache-Control: no-cache, must-revalidate");
        switch ($resource_type) {
            case 'json':
                $output = json_encode($data);
                header('Content-Type: application/json');
                break;
            case 'xml':
                $output = xmlrpc_encode($data);
                header('Content-Type: application/xml');
                break;
            case 'yml':
            case 'yaml':
                $output = yaml_emit($data);
                header('Content-Type: text/x-yaml');
                break;
            case 'txt':
                $output = var_dump($data);
                header('Content-Type: text/plain');
                break;
            case 'html':
            case 'css':
                $output = $data;
                header('Content-Type: text/'.$resource_type);
                break;
            default:
                $output = var_dump($data);
        }
        return AUTO_ENCODE_BASE64 ? base64_encode($output) : $output;
    }

    private function process_image(string $resource_type, $data) {
        //TODO: da implementare
        echo $resource_type;
        return $data;
    }
}