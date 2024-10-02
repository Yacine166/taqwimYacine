<?php
namespace App\Enums;
enum EmployeesNumber :string
{
  case ZERO = "0-9" ;
  case ONE = "10-50" ;
  case TWO = "51-100" ;
  case THREE = "100+" ;
}