<?php

Class PersonasGateway 
{
    private $conn;
    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    public function getAll($params) {
        if (isset($this->conn)) {
            $page = $_GET["page"] ?? 1;
            $limit_start = ($page - 1) * 20;

            $sql = "SELECT p.* FROM personas p LIMIT $limit_start,20";
            $stmt = $this->conn->query($sql);
            $data = [];

            $data["type"] = "people";
            $data["page"] = $page;

            $count = $this->conn->query("SELECT COUNT(id) c FROM personas p ");
            while ($row = $count->fetch(PDO::FETCH_ASSOC)) {
                $data["count"] = $row["c"];
            }

            $data["page_count"] = ceil($data["count"] / 20);

            if ($page < $data["count"]/20) {
                $next_page = $page + 1;
                $data["next_page"] = "http://localhost/peliculas/api/personas?page={$next_page}";
            } else {
                $data["next_page"] = null;
            }

            if($page > 1) {
                $previous_page = $page - 1;
                $data["previous_page"] = "http://localhost/peliculas/api/personas?page={$previous_page}";
            } else {
                $data["previous_page"] = null;
            }
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $person = [];
                $person["id"] = $row["id"];
                $person["name"] = $row["name"];
                $person["birth_date"] = $row["birth_date"];
                $person["death_date"] = $row["death_date"];
                $person["birth_place"] = $row["birth_place"];
                $person["bio"] = $row["bio"];
                $person["image"] = $row["image"];

                $filmography = $this->conn->query("SELECT p.* FROM peliculas p
                    JOIN peliculas_personas_roles ppr ON ppr.id_pelicula = p.id
                    WHERE ppr.id_persona = {$row['id']}
                    GROUP BY p.id;");

                while ($row2 = $filmography->fetch(PDO::FETCH_ASSOC)) {
                    $film = [];
                    $film["id"] = $row2["id"];
                    $film["title"] = $row2["title"];
                    $film["original_title"] = $row2["original_title"];
                    $film["year"] = $row2["year"];
                    $film["length"] = $row2["length"];
                    $film["rating"] = $row2["rating"];
                    $film["overview"] = $row2["overview"];
                    $film["image"] = $row2["image"];

                    $roles = $this->conn->query("SELECT r.name as rol FROM roles r
                    JOIN peliculas_personas_roles ppr ON ppr.id_rol = r.id
                    WHERE ppr.id_pelicula = {$row2['id']} AND ppr.id_persona = {$row['id']};");

                    while ($row3 = $roles->fetch(PDO::FETCH_ASSOC)) {
                        $film["roles"][] = $row3["rol"];
                    }

                    $person["filmography"][] = $film;
                }
                
                $data["results"][] = $person;
            }
        } else {
            $data = ["error" => true, "message" => "No se ha podido conectar a la base de datos"];
        }

        return $data;
    }

    public function getPersona($id) {
        if (isset($this->conn)) {
            $sql = "SELECT p.* FROM personas p WHERE id = $id";

            $stmt = $this->conn->query($sql);
            $data = [];

            $data["type"] = "people";
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $person = [];
                $person["id"] = $row["id"];
                $person["name"] = $row["name"];
                $person["birth_date"] = $row["birth_date"];
                $person["death_date"] = $row["death_date"];
                $person["birth_place"] = $row["birth_place"];
                $person["bio"] = $row["bio"];
                $person["image"] = $row["image"];

                $filmography = $this->conn->query("SELECT p.* FROM peliculas p
                    JOIN peliculas_personas_roles ppr ON ppr.id_pelicula = p.id
                    WHERE ppr.id_persona = {$row['id']}
                    GROUP BY p.id;");

                while ($row2 = $filmography->fetch(PDO::FETCH_ASSOC)) {
                    $film = [];
                    $film["id"] = $row2["id"];
                    $film["title"] = $row2["title"];
                    $film["original_title"] = $row2["original_title"];
                    $film["year"] = $row2["year"];
                    $film["length"] = $row2["length"];
                    $film["rating"] = $row2["rating"];
                    $film["overview"] = $row2["overview"];
                    $film["image"] = $row2["image"];

                    $roles = $this->conn->query("SELECT r.name as rol FROM roles r
                    JOIN peliculas_personas_roles ppr ON ppr.id_rol = r.id
                    WHERE ppr.id_pelicula = {$row2['id']} AND ppr.id_persona = {$row['id']};");

                    while ($row3 = $roles->fetch(PDO::FETCH_ASSOC)) {
                        $film["roles"][] = $row3["rol"];
                    }

                    $person["filmography"][] = $film;
                }
                
                $data["results"] = $person;
            }
    
        } else {
            $data = ["error" => true, "message" => "No se ha podido conectar a la base de datos"];
        }
        
        return $data;
    }
}