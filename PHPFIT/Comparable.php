<?php
interface PHPFIT_Comparable {

    /**
     * @param mixed $other
     * @return a negative integer, zero, or a positive integer as this object is less than, equal to, or greater than the specified object.
     */
    public function compareTo($other);
}
?>