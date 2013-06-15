<?php

namespace Utilities;


class CoreSearchLink
{
    private $query;
    private $base_url;
    private $path;

    function __construct($base_url, $path, $query) {
        $this->base_url = $base_url;
        $this->path = $path;
        $this->query = $query;
    }

    public function getLink() {
        return $this->base_url . "/" . $this->path . "/" . $this->query;
    }
}