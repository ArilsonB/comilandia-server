<?php
    return [
        'send' => function(){
            if(isset($_POST)){
                include realpath('./class/db.class.php');
                $query = "INSERT INTO recipes SET name = :name, ingredients = :ingredients, recipe = :recipe, image = :image";
                if($query = $db->prepare($query)){
                    $query_arr = array(
                        ':name' => $_POST['name'],
                        ':ingredients' => $_POST['ingredients'],
                        ':recipe' => $_POST['recipe'],
                        ':image' => 'image_url'
                    );
                    $query->execute($query_arr);
                    Router::go('/new-recipe');
                }else{
                    Router::go('/');
                }
            }else{
                Router::go('/');
            }
        }
    ];
?>