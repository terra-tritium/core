<?php

namespace App\Models;

class Alien
{
  protected $code;
  protected $name;
  protected $level;
  protected $metal;
  protected $uranium;
  protected $cristal;
  protected $craft;
  protected $bomber;
  protected $cruiser;
  protected $scout;
  protected $stealth;
  protected $flagship;

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