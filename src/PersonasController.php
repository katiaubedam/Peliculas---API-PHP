<?php

Class PersonasController 
{
    public function __construct(private PersonasGateway $gateway) {

    }

    public function processRequest(string $method, $id, $params) : void {
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method, $params);
        }
    }
    private function processResourceRequest(string $method, string $id) : void {
        switch ($method) {
            case "GET":
                $data = $this->gateway->getPersona($id);

                if (count($data) > 0) {
                    echo json_encode($data);
                } else {
                    echo json_encode([
                        "error" => true,
                        "message" => "Persona no encontrado"
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
                        "message" => "No se han encontrado personas"
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