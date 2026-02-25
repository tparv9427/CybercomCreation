<?php
class Sdp{
    static public function run(){
        $front = new Core_Controllers_Front();
        $front->run();
    }

    static public function getModel($modelName){
        $model = array_map('ucfirst', explode("/",$modelName));
        $model = sprintf("%s_model_%s", $model[0], $model[1]);
        return new $model();
    }

        static public function getBlock($blockName){
        $block = array_map('ucfirst', explode("/",$blockName));
        $block = sprintf("%s_block_%s", $block[0], $block[1]);
        return new $block();
    }
}
?>