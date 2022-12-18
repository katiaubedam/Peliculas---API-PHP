<?php

Class PeliculasGateway 
{
    private $conn;
    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    public function getAll($params) {
        if (isset($this->conn)) {
            $page = $_GET["page"] ?? 1;
            $limit_start = ($page - 1) * 20;

            $sql_params = "";
            if (isset($params["genre"])) {
                $sql_params .= " JOIN peliculas_generos pg ON pg.id_pelicula = p.id AND pg.id_genero={$params['genre']}";
            }
            if (isset($params["country"])) {
                $sql_params .= " JOIN peliculas_paises pp ON pp.id_pelicula = p.id AND pp.id_pais={$params['country']}";
            }
            if (isset($params["year"])) {
                $sql_params .= " WHERE p.year={$params['year']}";
            }

            $sql = "SELECT p.* FROM peliculas p {$sql_params} LIMIT $limit_start,20";
            $stmt = $this->conn->query($sql);
            $data = [];

            $data["type"] = "movie";
            $data["page"] = $page;

            $count = $this->conn->query("SELECT COUNT(id) c FROM peliculas p {$sql_params}");
            while ($row = $count->fetch(PDO::FETCH_ASSOC)) {
                $data["count"] = $row["c"];
            }

            $data["page_count"] = ceil($data["count"] / 20);

            if ($page < $data["count"]/20) {
                $next_page = $page + 1;
                $data["next_page"] = "http://localhost/peliculas/api/peliculas?page={$next_page}";
                if (isset($params["genre"])) $data["next_page"] .= "&genre={$params['genre']}";
                if (isset($params["country"])) $data["next_page"] .= "&country={$params['country']}";
                if (isset($params["year"])) $data["next_page"] .= "&year={$params['year']}";
            } else {
                $data["next_page"] = null;
            }

            if($page > 1) {
                $previous_page = $page - 1;
                $data["previous_page"] = "http://localhost/peliculas/api/peliculas?page={$previous_page}";
                if (isset($params["genre"])) $data["previous_page"] .= "&genre={$params['genre']}";
                if (isset($params["country"])) $data["previous_page"] .= "&country={$params['country']}";
                if (isset($params["year"])) $data["previous_page"] .= "&year={$params['year']}";
            } else {
                $data["previous_page"] = null;
            }
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $film = [];
                $film["id"] = $row["id"];
                $film["title"] = $row["title"];
                $film["original_title"] = $row["original_title"];
                $film["year"] = $row["year"];
                $film["length"] = $row["length"];
                $film["rating"] = $row["rating"];
                $film["overview"] = $row["overview"];
                $film["image"] = $row["image"];
                $cast = $this->conn->query("SELECT per.id, per.name, per.birth_date, per.death_date, per.birth_place, image FROM personas per
                    JOIN peliculas_personas_roles ppr ON ppr.id_persona = per.id
                    WHERE ppr.id_pelicula = {$row['id']} AND ppr.id_rol = 1");

                while ($row2 = $cast->fetch(PDO::FETCH_ASSOC)) {
                    $film["cast"][] = $row2;
                }

                $directors = $this->conn->query("SELECT per.id, per.name, per.birth_date, per.death_date, per.birth_place, image FROM personas per
                    JOIN peliculas_personas_roles ppr ON ppr.id_persona = per.id
                    WHERE ppr.id_pelicula = {$row['id']} AND ppr.id_rol = 2");

                while ($row2 = $directors->fetch(PDO::FETCH_ASSOC)) {
                    $film["directors"][] = $row2;
                }

                $genres = $this->conn->query("SELECT g.name FROM generos g
                    JOIN peliculas_generos pg ON pg.id_genero = g.id
                    WHERE pg.id_pelicula = {$row['id']};");
                
                while ($row2 = $genres->fetch(PDO::FETCH_ASSOC)) {
                    $film["genres"][] = $row2["name"];
                }

                $countries = $this->conn->query("SELECT p.country FROM paises p
                    JOIN peliculas_paises pp ON pp.id_pais = p.id
                    WHERE pp.id_pelicula = {$row['id']};");
                
                while ($row2 = $countries->fetch(PDO::FETCH_ASSOC)) {
                    $film["countries"][] = $row2["country"];
                }
                
                $data["results"][] = $film;
            }
        } else {
            $data = ["error" => true, "message" => "No se ha podido conectar a la base de datos"];
        }

        return $data;
    }

    public function getPelicula($id) {
        if (isset($this->conn)) {
            $sql = "SELECT * FROM peliculas WHERE id = $id";

            $stmt = $this->conn->query($sql);
            $data = [];

            $data["type"] = "movie";
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $film = [];
                $film["id"] = $row["id"];
                $film["title"] = $row["title"];
                $film["original_title"] = $row["original_title"];
                $film["year"] = $row["year"];
                $film["length"] = $row["length"];
                $film["rating"] = $row["rating"];
                $film["overview"] = $row["overview"];
                $film["image"] = $row["image"];
                $cast = $this->conn->query("SELECT per.id, per.name, per.birth_date, per.death_date, per.birth_place, image FROM personas per
                    JOIN peliculas_personas_roles ppr ON ppr.id_persona = per.id
                    WHERE ppr.id_pelicula = {$row['id']} AND ppr.id_rol = 1");

                while ($row2 = $cast->fetch(PDO::FETCH_ASSOC)) {
                    $film["cast"][] = $row2;
                }

                $directors = $this->conn->query("SELECT per.id, per.name, per.birth_date, per.death_date, per.birth_place, image FROM personas per
                    JOIN peliculas_personas_roles ppr ON ppr.id_persona = per.id
                    WHERE ppr.id_pelicula = {$row['id']} AND ppr.id_rol = 2");

                while ($row2 = $directors->fetch(PDO::FETCH_ASSOC)) {
                    $film["directors"][] = $row2;
                }

                $genres = $this->conn->query("SELECT g.name FROM generos g
                    JOIN peliculas_generos pg ON pg.id_genero = g.id
                    WHERE pg.id_pelicula = {$row['id']};");
                
                while ($row2 = $genres->fetch(PDO::FETCH_ASSOC)) {
                    $film["genres"][] = $row2["name"];
                }

                $countries = $this->conn->query("SELECT p.country FROM paises p
                    JOIN peliculas_paises pp ON pp.id_pais = p.id
                    WHERE pp.id_pelicula = {$row['id']};");
                
                while ($row2 = $countries->fetch(PDO::FETCH_ASSOC)) {
                    $film["countries"][] = $row2["country"];
                }
                
                $data["results"] = $film;
            }
    
        } else {
            $data = ["error" => true, "message" => "No se ha podido conectar a la base de datos"];
        }
        
        return $data;
    }

    public function getSearch($params) {
        if (isset($this->conn)) {
            $term = $params["search"];
            $page = $_GET["page"] ?? 1;
            $limit_start = ($page - 1) * 20;

            $sql_params = "";
            if (isset($params["genre"])) {
                $sql_params .= " JOIN peliculas_generos pg ON pg.id_pelicula = p.id AND pg.id_genero={$params['genre']}";
            }
            if (isset($params["country"])) {
                $sql_params .= " JOIN peliculas_paises pp ON pp.id_pelicula = p.id AND pp.id_pais={$params['country']}";
            }

            $sql = "SELECT p.* FROM peliculas p {$sql_params} WHERE p.title LIKE '%{$term}%' LIMIT $limit_start,20";

            $stmt = $this->conn->query($sql);
            $data = [];

            $data["type"] = "movie";
            $data["page"] = $page;

            $count = $this->conn->query("SELECT COUNT(id) c FROM peliculas p {$sql_params} WHERE p.title LIKE '%{$term}%'");
            while ($row = $count->fetch(PDO::FETCH_ASSOC)) {
                $data["count"] = $row["c"];
            }

            $data["page_count"] = ceil($data["count"] / 20);

            if ($page < $data["count"]/20) {
                $next_page = $page + 1;
                $data["next_page"] = "http://localhost/peliculas/api/peliculas?page={$next_page}&search={$term}";
                if (isset($params["genre"])) $data["next_page"] .= "&genre={$params['genre']}";
                if (isset($params["country"])) $data["next_page"] .= "&country={$params['country']}";
            } else {
                $data["next_page"] = null;
            }

            if($page > 1) {
                $previous_page = $page - 1;
                $data["previous_page"] = "http://localhost/peliculas/api/peliculas?page={$previous_page}&search={$term}";
                if (isset($params["genre"])) $data["previous_page"] .= "&genre={$params['genre']}";
                if (isset($params["country"])) $data["previous_page"] .= "&country={$params['country']}";
            } else {
                $data["previous_page"] = null;
            }
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $film = [];
                $film["id"] = $row["id"];
                $film["title"] = $row["title"];
                $film["original_title"] = $row["original_title"];
                $film["year"] = $row["year"];
                $film["length"] = $row["length"];
                $film["rating"] = $row["rating"];
                $film["overview"] = $row["overview"];
                $film["image"] = $row["image"];
                $cast = $this->conn->query("SELECT per.name, per.birth_date, per.death_date, per.birth_place, image FROM personas per
                    JOIN peliculas_personas_roles ppr ON ppr.id_persona = per.id
                    WHERE ppr.id_pelicula = {$row['id']} AND ppr.id_rol = 1");

                while ($row2 = $cast->fetch(PDO::FETCH_ASSOC)) {
                    $film["cast"][] = $row2;
                }

                $directors = $this->conn->query("SELECT per.name, per.birth_date, per.death_date, per.birth_place, image FROM personas per
                    JOIN peliculas_personas_roles ppr ON ppr.id_persona = per.id
                    WHERE ppr.id_pelicula = {$row['id']} AND ppr.id_rol = 2");

                while ($row2 = $directors->fetch(PDO::FETCH_ASSOC)) {
                    $film["directors"][] = $row2;
                }

                $genres = $this->conn->query("SELECT g.name FROM generos g
                    JOIN peliculas_generos pg ON pg.id_genero = g.id
                    WHERE pg.id_pelicula = {$row['id']};");
                
                while ($row2 = $genres->fetch(PDO::FETCH_ASSOC)) {
                    $film["genres"][] = $row2["name"];
                }

                $countries = $this->conn->query("SELECT p.country FROM paises p
                    JOIN peliculas_paises pp ON pp.id_pais = p.id
                    WHERE pp.id_pelicula = {$row['id']};");
                
                while ($row2 = $countries->fetch(PDO::FETCH_ASSOC)) {
                    $film["countries"][] = $row2["country"];
                }
                
                $data["results"][] = $film;
            }
    
        } else {
            $data = ["error" => true, "message" => "No se ha podido conectar a la base de datos"];
        }
        
        return $data;
    }
}