<?php

Class PeliculasController 
{
    public function __construct(private PeliculasGateway $gateway) {

    }

    public function processRequest(string $method, $id, $params) : void {
        if (isset($params["search"])) {
            $this->processSearchRequest($method, $params);
        } else {
            if ($id) {
                $this->processResourceRequest($method, $id);
            } else {
                $this->processCollectionRequest($method, $params);
            }
        }
    }
    private function processResourceRequest(string $method, string $id) : void {
        switch ($method) {
            case "GET":
                $data = $this->gateway->getPelicula($id);

                if (count($data) > 0) {
                    echo json_encode($data);
                } else {
                    echo json_encode([
                        "error" => true,
                        "message" => "Película no encontrada"
                    ]);
                }

                break;
            default:
                echo json_encode([
                    "error" => true,
                    "message" => "Método $method no soportado"
                ]);
        }
    }

    private function processCollectionRequest(string $method, array $params) : void {
        switch ($method) {
            case "GET":
                $data = $this->gateway->getAll($params);

                if (count($data) > 0) {
                    echo json_encode($data);
                } else {
                    echo json_encode([
                        "error" => true,
                        "message" => "Película no encontrada"
                    ]);
                }
                
                break;
            default:
                echo json_encode([
                    "error" => true,
                    "message" => "Método $method no soportado"
                ]);
        }
    }

    private function processSearchRequest(string $method, array $params) : void {
        switch ($method) {
            case "GET":
                $data = $this->gateway->getSearch($params);

                if (count($data) > 0) {
                    echo json_encode($data);
                } else {
                    echo json_encode([
                        "error" => true,
                        "message" => "No se encontraron resultados"
                    ]);
                }
                break;
            default:
                echo json_encode([
                    "error" => true,
                    "message" => "Método $method no soportado"
                ]);
        }
    }
}