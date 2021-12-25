<?php

namespace Luizfnunes\PeanutRouter;

use Luizfnunes\PeanutRouter\RouterPattern;
use Luizfnunes\PeanutRouter\RouterError;

class Router
{
    private string $baseUrl;
    private int $deepthUrl;
    private string $requestMethod;
    private string $requestUri;
    private array $routes;

    public function __construct(string $baseUrl, int $deepthUrl = 0)
    {
        /**
        * Valores iniciais da requisição
        */
        $this->baseUrl = $baseUrl;
        $this->deepthUrl = $deepthUrl;
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestUri = $this->getUri();
    }

    private function getUri()
    {
        /**
        * Captura a url removendo a url de base, usando o valor
        * da profundidade de URL
        */
        $baseUri = $_SERVER['REQUEST_URI'];
        $tmpUri = array_filter(explode('/', $baseUri));
        $tmpUri = array_slice($tmpUri, $this->deepthUrl);
        return "/".implode("/", $tmpUri);
    }

    public function addPattern(string $name, string $pattern)
    {
        /** 
         * Adiciona padrão de url às rotas 
        */
        if(!RouterPattern::extends($name, $pattern)){
            throw new \Exception("Error in RouterPattern: invalid pattern!", 1);
        }
    }

    public function get(string $route, array $call)
    {
        /* 
        * Adiciona rota no metodo GET
        */
        $this->routes['GET'][$route] = $call;
    }

    public function post(string $route, array $call)
    {
        /* 
        * Adiciona rota no metodo POST
        */
        $this->routes['POST'][$route] = $call;
    }

    public function put(string $route, array $call)
    {
        /* 
        * Adiciona rota no metodo PUT
        */
        $this->routes['PUT'][$route] = $call;
    }

    public function patch(string $route, array $call)
    {
        /* 
        * Adiciona rota no metodo GET
        */
        $this->routes['PATCH'][$route] = $call;
    }

    public function delete(string $route, array $call)
    {
        /* 
        * Adiciona rota no metodo DELETE
        */
        $this->routes['DELETE'][$route] = $call;
    }

    public function run()
    {
        /**
         * Executa o controle de rotas
         */
        $this->error = false;
        $this->verifyRoute();
        $this->verifyParamRoute();
        RouterError::registerError('2A', 'Route '.$this->requestUri.' not found!');
    }

    private function verifyRoute()
    {
        /**
         * Verifica as rotas simples
         */
        $currentMethod = $this->requestMethod;
        $currentUri = $this->requestUri;
        foreach ($this->routes as $routeMethod => $routeNamed) {
            // Se o metodo atual é a chave da rota
            if($routeMethod == $currentMethod){
                foreach ($routeNamed as $name => $call) {
                    // Se o nome da rota é igual a rota chamada
                    if($name == $currentUri){
                        $controller = $call[0];
                        $method = $call[1];
                        // chama o controller
                        $this->call_controller($controller, $method);
                    }
                }
            }
        }
    }

    private function verifyParamRoute()
    {
        /**
         * Verifica as rotas com parâmetros
         */
        $currentMethod = $this->requestMethod;
        // Quebra a URI atual
        $currentUri = array_values(array_filter(explode('/', $this->requestUri)));
        // Cria um array apenas com as rotas parametrizadas
        $routes = $this->routes[$currentMethod];
        foreach ($routes as $name => $call) {
            // se a rota possui parametros
            if( preg_match_all('/{[a-zA-z0-9_-]*}/', $name) ){
                // Quebra a rota por name
                $tmpUri = array_values(array_filter(explode('/', $name)));
                // Se a rota possui mesmo tamanho da uri
                if(count($tmpUri) == count($currentUri)){
                    // cria uma rota temp para verificar
                    $tmpRoute = "";
                    // percorre a rota
                    $params = [];
                    for($i=0; $i < count($tmpUri); $i++){
                        // verifica se não é igual
                        if($tmpUri[$i] != $currentUri[$i]){
                            // Verifica se o padrão existe no RouterPattern
                            if(array_key_exists($tmpUri[$i], RouterPattern::$list)){
                                // verifica se o pattern bate com o parametro
                                $pattern = RouterPattern::$list[$tmpUri[$i]];
                                if(preg_match($pattern, $currentUri[$i])){
                                    // adiciona a rota
                                    $tmpRoute .= "/".$tmpUri[$i];
                                    // adiciona o parametro
                                    array_push($params, $currentUri[$i]);
                                }
                            }else{
                                // Se o padrão não existe, não é a rota
                                break;
                            }
                        }else{
                            // Se é igual adiciona a rota temp
                            $tmpRoute .= "/".$currentUri[$i];
                        }
                    }
                    if(array_key_exists($tmpRoute, $this->routes[$currentMethod])){
                        // se a rota existe
                        $controller = $this->routes[$currentMethod][$tmpRoute][0];
                        $method = $this->routes[$currentMethod][$tmpRoute][1];
                        $this->call_controller($controller, $method, $params);
                    }
                }
            }
        }
    }

    private function call_controller(mixed $controller, string $method, array $params = [])
    {
        /**
         * Executa o controler associado a rota
         */
        if(class_exists($controller)){
            $controllerInstance = new $controller();
            // Verifica se o metodo existe
            if(method_exists($controllerInstance, $method)){
                // chama e sai
                call_user_func_array([$controllerInstance, $method],[$params]);
                exit;
            }
            RouterError::registerError('1A', 'Method not found in controller: '.$controller);
        }
        RouterError::registerError('1B', 'Controller '.$controller.' not found!');
    }

    public function hasError()
    {
        /**
         * verifica os erros
         */
        return RouterError::hasError();
    }

    public function getErrors()
    {
        /**
         * Retorna os erros
         */
        return RouterError::getErrors();
    }

    public function redirect(string $uri)
    {
        /**
         * Redireciona para rota
         */
        header('Location: '.$this->baseUrl.$uri);
    }
}