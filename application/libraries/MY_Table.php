<?php 
/**
 * Some helpers
 * 
 * add_column
 * remove_colum
 * add_anchor($id_column_name, $value_column_name, $path)
 * transpose
 * 
 *
 */
Class MY_Table extends CI_Table {

    public $columns;

    /**
     * Transpose array
     * 
     * @param $array
     * @return array
     */

    /**
     * Add column to table 
     * 
     * @return unknown_type
     */
    function add_column(){
        // Is heading empty?
        if (empty($this->heading)){
            // no heading yet
            $columns = $this->_flip($this->rows);
            $args = func_get_args();
            $columns[] = (is_array($args[0])) ? $args[0] : $args;
            $this->rows = $this->_flip($columns);
        } else { 
            // Assume that the first element is heading
            $args = func_get_args();
            $col = (is_array($args[0])) ? $args[0] : $args;
            $heading = array_shift($col);
            // add columns...
            $columns = $this->_flip($this->rows);
            $columns[] = $col;
            $this->rows = $this->_flip($columns);
            // ...and heading
            $this->heading[] = $heading;
        }
    }

    // Remove column from table
    function remove_column($name){
        if(empty($this->heading))
            return;
        $key = array_search($name, $this->heading);
        $columns = $this->_flip($this->rows);
        unset($columns[$key]);
        unset($this->heading[$key]);
        $this->rows = $this->_flip($columns);
    }

    // Transpose table
    function transpose(){
        if (!empty($this->heading))
            $this->heading = array(); // Empty heading
        $this->rows = $this->_flip($this->rows);
    }

    // Create anchor for table row
    function add_anchor($id_column, $value_column, $path){
        $CI = & get_instance();
        $CI->load->helper('url');
        if (empty($this->heading))
            return;
        $id_key = array_search($id_column, $this->heading);
        $val_key = array_search($value_column, $this->heading);
        foreach ($this->rows as $key => $var) {
            $this->rows[$key][$val_key] = $this->_add_anchor($this->rows[$key][$val_key], $this->rows[$key][$id_key], $path);
        }
    }

    private function _add_anchor($value, $id, $path){
        return anchor($path. "/" .$id, $value);
    }

    private function _flip($array){
        $flipped = array();
        foreach ($array as $key => $subarray){
            foreach ($subarray as $subkey => $subvar) {
                $flipped[$subkey][$key] = $subvar;
            }
        }
        return $flipped;
    }

}