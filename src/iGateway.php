<?php

namespace Jeeb;

interface iGateway {
    public function issue();
    public function status();
    public function seal();
}