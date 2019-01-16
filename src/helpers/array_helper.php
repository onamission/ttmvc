<?php

    /**
     * ensureArray
     * Makes sure a variable is an array
     *
     * @param mixed $list
     * @return array
     */
    function ensureArray($list){
        if (is_string($list)){
            // make sure we have consistent spacing after commas
            $list = str_replace(', ', ',', $list);
            $list = explode(',', $list);
        }
        return array_values($list);
    }