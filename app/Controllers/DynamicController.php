<?php



namespace App\Controllers;





class DynamicController extends BaseController

{

    public function execute($controllerName, $methodName = "index", $param = null, $param2 = null)
    {

        /* 
        print_r($controllerName);
        echo "\n";
        print_r($methodName);
        echo "\n";
        print_r($param);
        echo "\n";
        print_r($param2);
        echo "\n";
        exit;
        */
        // Construa o nome completo do controlador

        $ignores = [
            'CronJob',
            'api'
        ];


        if (!session()->has('usr_id') && $controllerName != "login" && !in_array($controllerName, $ignores)) {
            return redirect()->to(base_url('login'));
        }

        $controllerClass = 'App\Controllers\\' . ucfirst($controllerName);
        // Verifique se o controlador existe
        if (!class_exists($controllerClass)) {
            echo 'Controlador não encontrado.';
            return response()->setStatusCode(404, 'Controlador não encontrado.');
        }

        //print_r($methodName);

        // Instancie o controlador
        $controller = new $controllerClass();
        // Verifique se o método existe no controlador
        if (!method_exists($controller, $methodName)) {
            echo 'Método não encontrado.';
            return response()->setStatusCode(404, 'Método não encontrado.');
        }


        // Chame o método

        if ($param != null && $param2 != null) {
            return $controller->$methodName($param, $param2);
        } else  if ($param != null) {
            return $controller->$methodName($param);
        }

        return $controller->$methodName();
    }
}
