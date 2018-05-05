<?php
namespace Mulwi\Search\Api\Data;

interface DocumentInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return mixed
     */
    public function getData();
}