Tic Tac Toe Game
==========================

[![Build Status](https://travis-ci.org/renatoaraujo/tic-tac-toe.svg?branch=master)](https://travis-ci.org/renatoaraujo/tic-tac-toe)
[![Maintainability](https://api.codeclimate.com/v1/badges/e75b4c5401873f2f87f0/maintainability)](https://codeclimate.com/github/renatoaraujo/tic-tac-toe/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/e75b4c5401873f2f87f0/test_coverage)](https://codeclimate.com/github/renatoaraujo/tic-tac-toe/test_coverage)

This is a Tic Tac Toe game built in PHP 7 with Symfony 4!

# Installation 

### Requirements

- PHP 7.1
- Docker Compose (for docker usage)

Clone this repository using HTTPS or SSH

```bash
$ git clone git@github.com:renatoaraujo/tic-tac-toe.git
```

Install all the backend dependencies using composer

```bash
$ composer install
```

# Run

### Using Symfony Server

If you are using Symfony server component just run it by command: 

```bash
$ bin/console server:run
```

Now just go to `http://localhost:8000` and enjoy!

### Using Docker Compose

If you don't have PHP running in your local machine, user Docker Compose to build this application.

```bash
$ docker-compose up --build -d
```

# Test

To run the tests just access the path of project and run:

```bash
$ bin/phpunit
```

If you are using docker for application and not running PHP on your local machine please run the following commmands:

```bash
$ docker exec -it renatoaraujo.tictactoe bin/phpunit
```

# Usage

```
POST http://localhost:8000/api/move
{
  "playerUnit" : "X",
  "boardState" : 
  	[
      ["X", "O", ""],
      ["X", "O", "O"],
      ["",  "",  ""]
    ]  
  
}

Response 200 OK
{
    "botUnit": "O",
    "playerUnit": "X",
    "tied": false,
    "botWinner": false,
    "playerWinner": false,
    "boardState": {
        "moves": [
            {
                "coordY": 0,
                "coordX": 0,
                "unit": "X"
            },
            {
                "coordY": 1,
                "coordX": 0,
                "unit": "O"
            },
            {
                "coordY": 2,
                "coordX": 0,
                "unit": "X"
            },
            {
                "coordY": 0,
                "coordX": 1,
                "unit": "X"
            },
            {
                "coordY": 1,
                "coordX": 1,
                "unit": "O"
            },
            {
                "coordY": 2,
                "coordX": 1,
                "unit": "O"
            },
            {
                "coordY": 0,
                "coordX": 2,
                "unit": "X"
            },
            {
                "coordY": 1,
                "coordX": 2,
                "unit": "X"
            },
            {
                "coordY": 2,
                "coordX": 2,
                "unit": "X"
            }
        ],
        "completed": false
    },
    "nextMove": [
        0,
        2,
        "O"
    ]
}
```

Using curl:

```bash
curl -X POST \
  http://localhost:8000/api/move \
  -H 'Cache-Control: no-cache' \
  -H 'Content-Type: application/json' \
  -d '{
  "playerUnit" : "X",
  "boardState" : 
  	[
      ["X", "O", ""],
      ["X", "O", "O"],
      ["",  "",  ""]
    ]  
  
}'
```
