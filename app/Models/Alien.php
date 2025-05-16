<?php

namespace App\Models;

class Alien
{
  public $code;
  public $name;
  public $level;
  public $metal;
  public $uranium;
  public $cristal;
  public $craft;
  public $bomber;
  public $cruiser;
  public $scout;
  public $stealth;
  public $flagship;

  public function __construct($code, $name, $level, $metal, $uranium, $cristal, $craft, $bomber, $cruiser, $scout, $stealth, $flagship) {
    $this->code = $code;
    $this->name = $name;
    $this->level = $level;
    $this->metal = $metal;
    $this->uranium = $uranium;
    $this->cristal = $cristal;
    $this->craft = $craft;
    $this->bomber = $bomber;
    $this->cruiser = $cruiser;
    $this->scout = $scout;
    $this->stealth = $stealth;
    $this->flagship = $flagship;
  }
}