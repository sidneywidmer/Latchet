# Latchet (Laravel4 Package)

##Important

This is not even an alpha version. There's still a lot of stuff going on. The docs aren't finished, the demo app is missing and some of the code needs to get polished. So please don't use the package - yet. If you want to keep up to date you can follow me on [Twitter](https://twitter.com/sidneywidmer "Twitter")

##Intro

Latchet takes the hassle out of PHP backed realtime apps. At its base, it's a extended version of  [Ratchet](https://github.com/cboden/Ratchet "Ratchet") to work nicely with laravel.

If you're finished setting up a basic WampServer, you'll have something like this:

	Latchet::topic('chat/room/{roomid}', 'ChatRoomController');

## Installation

### Earlybird

Until i submit the pakage to packagist, include it directly from github.
    
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/sidneywidmer/latchet"
        }
    ],
    "require": {
        "sidney/latchet": "dev-master"
    }

### Required setup

In the `require` key of `composer.json` file add the following

    "sidney/latchet": "dev-master"

Run the Composer update comand

    $ composer update

In your `config/app.php` add `'Sidney\Latchet\LatchetServiceProvider'` to the end of the `$providers` array

    'providers' => array(

        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Auth\AuthServiceProvider',
        ...
        'Sidney\Latchet\LatchetServiceProvider',

    ),

At the end of `config/app.php` add `'Latchet'    => 'Sidney\Latchet\LatchetFacade'` to the `$aliases` array

    'aliases' => array(

        'App'        => 'Illuminate\Support\Facades\App',
        'Artisan'    => 'Illuminate\Support\Facades\Artisan',
        ...
        'Latchet'    => 'Sidney\Latchet\LatchetFacade',

    ),

### Configuration

Publish the config file with the following artisan command: `php artisan config:publish sidney/latchet`

There are not a lot of configuration options, the most important will be `enablePush` and `zmqPort`. This requires some extra configuration on your server and is discussed in the next section.

The rest of the options should be pretty self self-explanatory. 

### Enable push

Braindump:

* Installing zermq; (in this order) ubuntu
* http://johanharjono.com/archives/633 (if this doesn't work, compile from source)
* http://www.zeromq.org/intro:get-the-software
* http://www.zeromq.org/bindings:php (eventualli apt-get install pkg-config, make)
* add extension=zmq.so to php.ini 
* check if extension loaded php -m 
* check if zeromq package (zlib1g) is installed dpkg --get-selections

## Usage

### Introduction

Like mentioned before, Latchet is based on Ratchet and just extends its functionality with some nice extra features like passing parameters in ***topics***. But Latchet also removes some of the flexibility Ratchet provides for the sake of simplicity. Latchet solely focuses on providing a WampServer for your aplications. 

#### Topics

I would really recommend you to read through the [Ratchet docs](http://socketo.me/docs/ "Ratchet docs"). They explain the basic principles very clearly. 

Once you get the hang of it, topics are really easy to understand. Imagine a standart laravel route as you know it. 

	Route::get('my/route/{parameter}', 'MyController@action');
	
Topics (or if you are familiar with other forms of messaging, channels) are the same for websockets connections.
Theres always a client which subscribes a topic. If other clients connect to the same topic, they can then broadcast messages to this subscribed topic or a specific client connected to this topic.

### Server

Everything we're doing in this section is just to set up a WampServer which will then be started from the command line and listen on incomming connections. Basically there are two different handlers we have to set up. One which handles different connection actions and (at least) one for our topic(s) actions. 

To clarify stuff:

***Connection actions are:***

* open
* close
* error

***Topic actions are:***

* subscribe
* publish
* call
* unsubscribe

#### tl;dr (a.k.a - the artisan way)

Generate all the necessary files with this artisan command and skip to 'Start the Server'
	
	php artisan latchet:generate 
	

#### Add a connection handler

First of all, we have 'register' a controller which defines how to react on different connection actions. So anytime a new connection to the server is establish, we'll ask the controller what to do. Easy as a pie:

	Latchet::connection('ConnectionController');
	


#### Add topic handlers

#### Start the server

### Client

#### Javascript / Legacy browsers

#### Demoapp


## License

Confide is free software distributed under the terms of the MIT license