<?php
interface PHPFIT_Comparable
{

    /**
     * Compare object
     * 
     * Returns a negative integer, zero, or a positive integer as this object 
     * is less than, equal to, or greater than the specified object.
     * 
     * @param mixed $other
     * @return integer
     */
    public function compareTo($other);
}

