<?php

namespace App\Models;

class Alien
{
  CONST ALIEN_INFESTA = 9996;
  CONST ALIEN_SIMBION = 9997;
  CONST ALIEN_TANTRA = 9998;
  CONST ALIEN_XANTII = 9999;

  public function get($code, $level) {
    switch ($code) {
      case 9996 : 
        switch ($level) {
          case 1 : new Alien($code, 'infesta', $level, 10000, 300, 100, 2, 0, 0, 0, 0, 0);
          case 2 : new Alien($code, 'infesta', $level, 20000, 500, 150, 4, 2, 0, 0, 0, 0);
          case 3 : new Alien($code, 'infesta', $level, 30000, 1000, 500, 15, 2, 0, 0, 0, 0);
          case 4 : new Alien($code, 'infesta', $level, 50000, 1500, 700, 25, 2, 0, 0, 0, 0);
          case 5 : new Alien($code, 'infesta', $level, 70000, 2000, 1000, 40, 5, 0, 0, 0, 0);
          case 6 : new Alien($code, 'infesta', $level, 100000, 5000, 2500, 55, 5, 1, 0, 0, 0);
          case 7 : new Alien($code, 'infesta', $level, 150000, 7000, 3000, 70, 10, 3, 0, 0, 0);
          case 8 : new Alien($code, 'infesta', $level, 200000, 10000, 5000, 90, 10, 5, 2, 0, 0);
          case 9 : new Alien($code, 'infesta', $level, 300000, 20000, 10000, 150, 15, 10, 5, 0, 0);
          case 10 : new Alien($code, 'infesta', $level, 500000, 40000, 20000, 200, 20, 10, 7, 2, 0);
          case 11 : new Alien($code, 'infesta', $level, 700000, 50000, 25000, 300, 20, 12, 10, 7, 0);
          case 12 : new Alien($code, 'infesta', $level, 1000000, 70000, 30000, 500, 25, 20, 10, 10, 0);
          case 13 : new Alien($code, 'infesta', $level, 1200000, 75000, 40000, 550, 27, 25, 12, 12, 0);
          case 14 : new Alien($code, 'infesta', $level, 1500000, 100000, 50000, 650, 35, 30, 15, 15, 0);
          case 15 : new Alien($code, 'infesta', $level, 2000000, 120000, 60000, 1000, 40, 35, 20, 20, 0);
          case 16 : new Alien($code, 'infesta', $level, 3000000, 150000, 75000, 1500, 45, 40, 30, 30, 0);
          case 17 : new Alien($code, 'infesta', $level, 5000000, 150000, 75000, 2000, 50, 50, 50, 50, 5);
          case 18 : new Alien($code, 'infesta', $level, 7000000, 200000, 100000, 2550, 65, 60, 50, 50, 10);
          case 19 : new Alien($code, 'infesta', $level, 10000000, 300000, 150000, 3100, 80, 80, 55, 55, 20);
          case 20 : new Alien($code, 'infesta', $level, 20000000, 500000, 200000, 8000, 150, 200, 100, 100, 40);
          case 21 : new Alien($code, 'infesta', $level, 50000000, 1000000, 500000, 20000, 1200, 500, 300, 200, 60);
          case 22 : new Alien($code, 'infesta', $level, 100000000, 2000000, 1000000, 50000, 2000, 1000, 500, 500, 150);
          case 23 : new Alien($code, 'infesta', $level, 200000000, 4000000, 2000000, 100000, 5000, 2000, 1000, 1000, 300);
          case 24 : new Alien($code, 'infesta', $level, 500000000, 8000000, 4000000, 250000, 15000, 4000, 2000, 2000, 700);
          case 25 : new Alien($code, 'infesta', $level, 1000000000, 10000000, 5000000, 500000, 25000, 10000, 5000, 5000, 1500);
          case 26 : new Alien($code, 'infesta', $level, 2000000000, 20000000, 10000000, 1000000, 45000, 10000, 15000, 10000, 2500);
        }
        break;
      case 9997 : 
        switch ($level) {
          case 1 : new Alien($code, 'simbion', $level, 10000, 300, 100, 2, 0, 0, 0, 0, 0);
          case 2 : new Alien($code, 'simbion', $level, 20000, 500, 150, 4, 2, 0, 0, 0, 0);
          case 3 : new Alien($code, 'simbion', $level, 30000, 1000, 500, 10, 3, 0, 0, 0, 0);
          case 4 : new Alien($code, 'simbion', $level, 50000, 1500, 700, 15, 5, 0, 0, 0, 0);
          case 5 : new Alien($code, 'simbion', $level, 70000, 2000, 1000, 25, 7, 2, 0, 0, 0);
          case 6 : new Alien($code, 'simbion', $level, 100000, 5000, 2500, 30, 10, 5, 0, 0, 0);
          case 7 : new Alien($code, 'simbion', $level, 150000, 7000, 3000, 45, 15, 7, 0, 0, 0);
          case 8 : new Alien($code, 'simbion', $level, 200000, 10000, 5000, 50, 20, 10, 2, 0, 0);
          case 9 : new Alien($code, 'simbion', $level, 300000, 20000, 10000, 75, 30, 15, 10, 0, 0);
          case 10 : new Alien($code, 'simbion', $level, 500000, 40000, 20000, 110, 40, 20, 10, 5, 0);
          case 11 : new Alien($code, 'simbion', $level, 700000, 50000, 25000, 125, 50, 25, 15, 5, 0);
          case 12 : new Alien($code, 'simbion', $level, 1000000, 70000, 30000, 150, 60, 30, 20, 10, 0);
          case 13 : new Alien($code, 'simbion', $level, 1200000, 75000, 40000, 170, 70, 35, 25, 10, 0);
          case 14 : new Alien($code, 'simbion', $level, 1500000, 100000, 50000, 200, 80, 45, 30, 10, 0);
          case 15 : new Alien($code, 'simbion', $level, 2000000, 120000, 60000, 250, 100, 60, 40, 20, 0);
          case 16 : new Alien($code, 'simbion', $level, 3000000, 150000, 75000, 300, 150, 80, 50, 50, 0);
          case 17 : new Alien($code, 'simbion', $level, 5000000, 150000, 75000, 500, 250, 100, 50, 50, 5);
          case 18 : new Alien($code, 'simbion', $level, 7000000, 200000, 100000, 500, 350, 150, 70, 70, 10);
          case 19 : new Alien($code, 'simbion', $level, 10000000, 300000, 150000, 900, 550, 200, 100, 100, 20);
          case 20 : new Alien($code, 'simbion', $level, 20000000, 500000, 200000, 1800, 1100, 400, 200, 200, 40);
          case 21 : new Alien($code, 'simbion', $level, 50000000, 1000000, 500000, 3000, 2200, 900, 500, 500, 100);
          case 22 : new Alien($code, 'simbion', $level, 100000000, 2000000, 1000000, 7000, 5000, 2000, 1100, 1100, 300);
          case 23 : new Alien($code, 'simbion', $level, 200000000, 4000000, 2000000, 15000, 11000, 4000, 2500, 2500, 500);
          case 24 : new Alien($code, 'simbion', $level, 500000000, 8000000, 4000000, 40000, 25000, 9000, 5000, 5000, 1500);
          case 25 : new Alien($code, 'simbion', $level, 1000000000, 10000000, 5000000, 100000, 50000, 18000, 11000, 11000, 3500);
          case 26 : new Alien($code, 'simbion', $level, 2000000000, 20000000, 10000000, 250000, 90000, 30000, 25000, 20000, 5000);
        }
        break;
      case 9998 : 
        switch ($level) {
          case 1 : new Alien($code, 'tantra', $level, 10000, 300, 100, 2, 0, 0, 0, 0, 0);
          case 2 : new Alien($code, 'tantra', $level, 20000, 500, 150, 4, 0, 0, 0, 2, 0);
          case 3 : new Alien($code, 'tantra', $level, 30000, 1000, 500, 5, 3, 0, 0, 5, 0);
          case 4 : new Alien($code, 'tantra', $level, 50000, 1500, 700, 10, 5, 0, 0, 10, 0);
          case 5 : new Alien($code, 'tantra', $level, 70000, 2000, 1000, 10, 7, 2, 0, 15, 0);
          case 6 : new Alien($code, 'tantra', $level, 100000, 5000, 2500, 20, 10, 5, 0, 30, 0);
          case 7 : new Alien($code, 'tantra', $level, 150000, 7000, 3000, 25, 10, 7, 0, 35, 0);
          case 8 : new Alien($code, 'tantra', $level, 200000, 10000, 5000, 30, 10, 10, 2, 40, 0);
          case 9 : new Alien($code, 'tantra', $level, 300000, 20000, 10000, 40, 15, 15, 5, 50, 0);
          case 10 : new Alien($code, 'tantra', $level, 500000, 40000, 20000, 50, 15, 15, 7, 75, 0);
          case 11 : new Alien($code, 'tantra', $level, 700000, 50000, 25000, 70, 20, 20, 10, 100, 0);
          case 12 : new Alien($code, 'tantra', $level, 1000000, 70000, 30000, 90, 25, 20, 15, 125, 0);
          case 13 : new Alien($code, 'tantra', $level, 1200000, 75000, 40000, 100, 30, 25, 15, 150, 0);
          case 14 : new Alien($code, 'tantra', $level, 1500000, 100000, 50000, 120, 35, 30, 20, 200, 0);
          case 15 : new Alien($code, 'tantra', $level, 2000000, 120000, 60000, 150, 40, 35, 25, 300, 0);
          case 16 : new Alien($code, 'tantra', $level, 3000000, 150000, 75000, 200, 50, 45, 30, 500, 0);
          case 17 : new Alien($code, 'tantra', $level, 5000000, 150000, 75000, 250, 70, 60, 40, 700, 5);
          case 18 : new Alien($code, 'tantra', $level, 7000000, 200000, 100000, 300, 90, 90, 50, 850, 10);
          case 19 : new Alien($code, 'tantra', $level, 10000000, 300000, 150000, 500, 150, 100, 70, 1000, 20);
          case 20 : new Alien($code, 'tantra', $level, 20000000, 500000, 200000, 1000, 300, 200, 140, 2000, 40);
          case 21 : new Alien($code, 'tantra', $level, 50000000, 1000000, 500000, 1500, 1200, 400, 250, 4500, 100);
          case 22 : new Alien($code, 'tantra', $level, 100000000, 2000000, 1000000, 3000, 2000, 1000, 800, 7000, 300);
          case 23 : new Alien($code, 'tantra', $level, 200000000, 4000000, 2000000, 7000, 5000, 2000, 1500, 10000, 500);
          case 24 : new Alien($code, 'tantra', $level, 500000000, 8000000, 4000000, 20000, 15000, 5000, 2500, 15000, 1500);
          case 25 : new Alien($code, 'tantra', $level, 1000000000, 10000000, 5000000, 50000, 20000, 10000, 7000, 30000, 3500);
          case 26 : new Alien($code, 'tantra', $level, 2000000000, 20000000, 10000000, 100000, 50000, 15000, 20000, 60000, 5000);
        }
        break;
      case 9998 : 
        switch ($level) {
          case 1 : new Alien($code, 'xantii', $level, 10000, 300, 100, 5, 0, 0, 0, 0, 0);
          case 2 : new Alien($code, 'xantii', $level, 20000, 500, 150, 10, 2, 0, 0, 0, 0);
          case 3 : new Alien($code, 'xantii', $level, 30000, 1000, 500, 15, 5, 0, 0, 0, 0);
          case 4 : new Alien($code, 'xantii', $level, 50000, 1500, 700, 25, 7, 0, 0, 0, 0);
          case 5 : new Alien($code, 'xantii', $level, 70000, 2000, 1000, 20, 10, 5, 0, 0, 0);
          case 6 : new Alien($code, 'xantii', $level, 100000, 5000, 2500, 20, 10, 5, 0, 0, 1);
          case 7 : new Alien($code, 'xantii', $level, 150000, 7000, 3000, 20, 10, 7, 0, 0, 2);
          case 7 : new Alien($code, 'xantii', $level, 200000, 10000, 5000, 30, 15, 10, 2, 0, 3);
          case 8 : new Alien($code, 'xantii', $level, 300000, 20000, 10000, 30, 25, 10, 5, 0, 5);
          case 9 : new Alien($code, 'xantii', $level, 500000, 40000, 20000, 110, 40, 10, 10, 5, 6);
          case 10 : new Alien($code, 'xantii', $level, 700000, 50000, 25000, 125, 50, 25, 15, 5, 7);
          case 11 : new Alien($code, 'xantii', $level, 1000000, 70000, 30000, 150, 60, 30, 20, 10, 10);
          case 12 : new Alien($code, 'xantii', $level, 1200000, 75000, 40000, 170, 70, 35, 25, 10, 12);
          case 13 : new Alien($code, 'xantii', $level, 1500000, 100000, 50000, 170, 80, 45, 30, 10, 15);
          case 14 : new Alien($code, 'xantii', $level, 2000000, 120000, 60000, 200, 100, 60, 40, 20, 20);
          case 15 : new Alien($code, 'xantii', $level, 3000000, 150000, 75000, 300, 150, 60, 45, 30, 30);
          case 16 : new Alien($code, 'xantii', $level, 5000000, 150000, 75000, 500, 250, 100, 45, 35, 40);
          case 17 : new Alien($code, 'xantii', $level, 7000000, 200000, 100000, 500, 350, 150, 50, 50, 60);
          case 18 : new Alien($code, 'xantii', $level, 10000000, 300000, 150000, 900, 550, 200, 60, 60, 90);
          case 19 : new Alien($code, 'xantii', $level, 20000000, 500000, 200000, 1800, 1100, 400, 100, 100, 130);
          case 20 : new Alien($code, 'xantii', $level, 50000000, 1000000, 500000, 3000, 2200, 900, 300, 300, 200);
          case 21 : new Alien($code, 'xantii', $level, 100000000, 2000000, 1000000, 7000, 5000, 2000, 1100, 1100, 500);
          case 22 : new Alien($code, 'xantii', $level, 200000000, 4000000, 2000000, 15000, 11000, 4000, 2500, 2500, 700);
          case 23 : new Alien($code, 'xantii', $level, 500000000, 8000000, 4000000, 40000, 25000, 9000, 5000, 5000, 1900);
          case 24 : new Alien($code, 'xantii', $level, 1000000000, 10000000, 5000000, 100000, 50000, 18000, 11000, 11000, 5500);
          case 25 : new Alien($code, 'xantii', $level, 2000000000, 20000000, 10000000, 250000, 90000, 30000, 25000, 20000, 10000);
        }
    }
  }
}