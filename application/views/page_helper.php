<?php

function page_name(): string {
    return application_name().' | '.ucwords($_ENV['controller']);
}

function application_name(): string {
    return APP_NAME;
}

function render_page() {
    $section = $_ENV['controller'];
    $page = $_ENV['action'];
    require_once "./application/views/$section/$page.php";
}

function render_footer() {
    require_once "./application/views/footer.php";
}

function image(string $name): string {
    echo application_url()."assets/images/$name";
}

function css(string $name = null): void {
    if (!isset($name)) $name = $_ENV['controller'];
    $path = application_url().'assets/styles/'.$name.'.css';
    if (file_exists("./assets/styles/$name.css"))
        echo '<link rel="stylesheet" type="text/css" href="'.$path.'"/>';
}

function javascript($name= null): void {
    if (!isset($name)) $name = $_ENV['controller'];
    $path = application_url().'assets/scripts/'.$name.'.js';
    if (file_exists("./assets/scripts/'.$name.'.js"))
        echo '<script src="'.$path.'"/>';
}

function root_url(): string {
    return (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
}

function application_url(): string {
    return root_url().APP_URL.'/';
}