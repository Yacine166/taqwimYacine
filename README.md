# Taqwim

## tax management for algerian companies

---

## table of content

1-   [Instalation](#1--instalation)

2-   [Api](#2--api)
- [Routes](#routes)
- [Headers](#headers)
- [Parameters](#parameters)

---

## 1- instalation

```
composer preconfig
```

### this is a first time setup; configurable in the file init.sh if mysql logins changed

### otherwise you can do it manually as shown below

### 1- clone repo

```
git clone https://github.com/zackAJ/taqwim
```

### 2- make .env file and copy .env.example

### 3- composer install

```
composer i
```

### 4- npm

```
npm i
```

### 5- create database named taqwim and run:

```
php artisan migrate:refresh --seed
```

### 6- run server

```
php artisan serve
```

## 2- Api


## Routes 
[Headers](#headers) - [Parameters](#parameters)


```
LABEL                       METHOD      ENDPOINT

CSRF                        GET         sanctum/csrf-cookie
REGIRSTER                   POST        register
LOGIN                       POST        login
LOGOUT                      POST        logout
GET USER                    GET         api/me
UPDATE USER                 PUT         api/me
UPDATE PASSWORD             PUT         api/me/password
UPDATE LANGUAGE             PUT         api/me/language
GET PARAMETERS              GET         api/me/parameters
STORE ORGANIZATION          POST        api/me/organizations
GET ORGANIZATION            GET         api/me/organizations/{organization}
GET ORGANIZATIONS           GET         api/me/organizations
UPDATE ORGANIZATION         PUT         api/me/organizations/{organization}
DELETE ORGANIZATION         DELETE      api/me/organizations/{organization}
GET NOTIF SETTINGS          GET         api/me/organizations/{organization}/categories
UPDATE NOTIF SETTINGS       PUT         api/me/organizations/{organization}/categories/{category}
GET ORG EVENTS              GET         api/me/organizations/{organization}/events
COMPLETE EVENT              PUT         api/me/organizations/{organization}/events/{event}
```

## Headers
[Routes](#routes) - [Parameters](#parameters)


REGIRSTER

```
{
  'name'       :  'test',
  'name'       :  'test',
  'email'      :  'test@test.com',
  'phone'      :  '0777121212',
  'password'   :  'testtest'
}
```

LOGIN

```
{
  'email'       :  'test@test.com',
  'email'       :  'test@test.com',
  'password'    :  'testtest'
}
```

UPDATE USER

```
{
  'name'         :  'testing name',
  'email'        :  'testing@email.com',
  'phone'        :  '0777121212',
  'password'     :  'testtest'
}
```

UPDATE PASSWORD

```
{
  'current_password'        :   'test',
  'password'                :   'testtest',
  'password_confirmation'   :   'testtest'
}
```

UPDATE LANGUAGE

```
{
  'locale'                :   'ar',
}
```

STORE ORGANIZATION

```
{
  'name'              :    "jibrish"
  'activity_sector'   :    "Production de biens"
  "employees_number"  :    "51-100",
  "tax_ability"       :    "Fixed",
  "cacobatph"          :    "Not Affiliated",
  "person"            :    "Physic"
}
```

UPDATE ORGANIZATION

```
{
  'name'              :     "jibrish"
  'activity_sector'   :     "Production de biens"
  "employees_number"  :     "51-100",
  "tax_ability"       :     "Fixed",
  "cacobatph"          :     "Not Affiliated",
  "person"            :     "Physic"
}
```

UPDATE NOTIF

```
{
  'is_notifable' : true,
  'days_before_notification' : 17
}
```

COMPLETE EVENT

```
{
  'completed' : true
}
```

### Parameters
 [Routes](#routes) - [Headers](#headers)


GET ORG EVENTS
```
api/me/organizations/{organization}/events?
```
*+*
```
completed=false
```
```
date=2023-09-13
```
```
month=10
```
```
search=cotisatio
```
```
categories=[1,2,3]
```
"# taqwim" 
