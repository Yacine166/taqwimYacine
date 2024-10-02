<?php
namespace App\Enums;

enum Operator: string
{
  case EQUAL = "=";
  case SMALLER = "<";
  case GREATER = ">";
  case SMALLER_EQUAL = "<=";
  case GREATER_EQUAL = ">=";
}