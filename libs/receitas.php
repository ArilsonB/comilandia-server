<?php
return [
    'json' => function(){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        include realpath('./class/db.class.php');
        if(isset($_GET['recipe'])){
            $recipe = "WHERE name LIKE '%$_GET[recipe]%'";
        }else{
            $recipe = '';
        }
        if(isset($_GET['limit'])){
            $limit = $_GET['limit'];
        }else{
            $limit = 20;
        }
        $wheres = "WHERE moderate = 'true'";
        $sql= "SELECT * FROM recipes $recipe LIMIT $limit";
        $query = $db->query($sql);
        if($query->rowCount()> 0){
            $recipes = array();
            foreach($query as $row){
                extract($row);
                $recipe = array(
                    "id" => $id,
                    "name" => $name,
                    "image" => $image,
                    "ingredients" => html_entity_decode($ingredients),
                    "recipe" => html_entity_decode($recipe)
                );
                array_push($recipes,$recipe);
            }
            http_response_code(200);
            echo json_encode($recipes);
        }else{
            http_response_code(404);
            echo json_encode(
                array("message" => "No products found.")
            );
        }
    }
];
?>