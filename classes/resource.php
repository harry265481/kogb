<?php
    class Resource {
        static function getResourceOutput($link, $id) {
            return mysqli_fetch_array(mysqli_query($link, "SELECT outputAmount FROM resources WHERE ID = {$id}"))[0];
        }
        
        static function getResourceName($link, $id) {
            return mysqli_fetch_array(mysqli_query($link, "SELECT name FROM resources WHERE ID = {$id}"))[0];
        }

        static function getResourceType($link, $id) {
            return mysqli_fetch_array(mysqli_query($link, "SELECT type FROM resources WHERE ID = {$id}"))[0];
        }
        
        static function getResourcePrice($link, $id) {
            return mysqli_fetch_array(mysqli_query($link, "SELECT currentPrice FROM resources WHERE ID = {$id}"))[0];
        }
        
        static function getResourceBasePrice($link, $id) {
            return mysqli_fetch_array(mysqli_query($link, "SELECT basePrice FROM resources WHERE ID = {$id}"))[0];
        }
        
        static function getResourceCategory($link, $id) {
            return mysqli_fetch_array(mysqli_query($link, "SELECT category FROM resources WHERE ID = {$id}"))[0];
        }
        
        static function getAllResourceIDs($link) {
            return mysqli_fetch_all(mysqli_query($link, "SELECT ID FROM resources"));
        }

        static function getResourceRecipe($link, $prod) {
            $recipe = json_decode(mysqli_fetch_array(mysqli_query($link, "SELECT recipe FROM resources WHERE ID = {$prod}"))[0]);
            $rrecipe = array();
            foreach($recipe as $r) {
                $rrecipe[$r[0]] = $r[1];
            }
            return $rrecipe;
        }
    }
?>