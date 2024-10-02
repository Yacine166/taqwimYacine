<?php

namespace App\Enums;

enum ActivitySector: string
{
  case ZERO = "Production de biens";
  case ONE = "Craft production establishments";
  case TWO = "Wholesale distribution";
  case THREE = "Importation";
  case FOUR = "Retail distribution";
  case FIVE = "Services";
  case SIX = "Exportation";
}
