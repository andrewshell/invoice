<?php
namespace Invoice\Domain;

interface Mapper
{
    public function all();
    public function byNumber($number);
}
