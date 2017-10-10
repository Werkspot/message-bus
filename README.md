# Werkspot \ MessageBus

[![Author](http://img.shields.io/badge/author-Werkspot-blue.svg?style=flat-square)](https://www.werkspot.com)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
[![Latest Version](https://img.shields.io/github/release/werkspot/message-bus.svg?style=flat-square)](https://github.com/werkspot/message-bus/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/werkspot/message-bus.svg?style=flat-square)](https://packagist.org/packages/werkspot/message-bus)

[![Build Status](https://img.shields.io/scrutinizer/build/g/werkspot/message-bus.svg?style=flat-square)](https://scrutinizer-ci.com/g/werkspot/message-bus/build)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/werkspot/message-bus.svg?style=flat-square)](https://scrutinizer-ci.com/g/werkspot/message-bus/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/werkspot/message-bus.svg?style=flat-square)](https://scrutinizer-ci.com/g/werkspot/message-bus)

## What this project is

A library capable of delivering a message to a destination synchronously or asynchronously,
 using some other queueing library.

The message to be delivered can be anything and the destination can be specified by any string.

A chain of middlewares can be configured (DeliveryChain), and the message will go through all those middlewares
 allowing us to do various things, like validating the message, start and commit a transaction, replace the destination 
 according to some criteria, perform some logging, or whatever we need to to before and/or after delivering the message.

## Why this project exists

A Message Bus can make a project very flexible and performant. We can easily create a Command Bus, Event Bus,
 Event Sourcing, Queueing, or any similar construction on top of this Message Bus.

## Usage 

The `MessageDispatcher` is the entry point to the message bus. There are already a few middlewares provided with
 this library, which you can find in `src/Bus/DeliveryChain`.

```php
    $bus = Bus::fromMiddlewareList($middleware1, $middleware2 /*,  ... */);
    
    $messageDispatcher = new MessageDispatcher($bus);
    
    $messageDispatcher->dispatchSynchronousMessage(
        $someObjectOrStringOrWhatever,      // some payload to deliver, persisted by the MessageRepository
        '{"deliver_to": "SomeServiceId"}',  // destination to be decoded by the delivery service (MessageDeliveryServiceInterface)
        []                                  // some whatever metadata
    );
```

If you need to deliver messages asynchronously, then you need to add the `AsynchronousDeliveryMiddleware` to the bus.

The `AsynchronousDeliveryMiddleware` depends on the `MessageQueueServiceInterface`. So you need to choose a
 queueing library and create an adapter of that library that implements the `MessageQueueServiceInterface`,
 so it can be injected in the `AsynchronousDeliveryMiddleware`.
 
For example:

```php
    $messageQueueService = new MessageQueueServiceAdapterThatImplementsMessageQueueServiceInterface(/* ... */);
    
    $bus = Bus::fromMiddlewareList(
        $middleware1, 
        $middleware2, 
        new AsynchronousDeliveryMiddleware($messageQueueService) 
        /*,  ... */
    );
    
    // Now you can send an Asynchronous (potentially Queued) message
    $messageDispatcher->dispatchQueuedMessage(
        $someObjectOrStringOrWhatever,       // some payload to deliver, persisted by the MessageRepository
        '{"deliver_to": "SomeServiceId"}',   // destination to be decoded by the delivery service (MessageDeliveryServiceInterface)
        [],                                  // some whatever metadata
        new DateTimeImmutable('2037-10-08'), // some (optional, future) delivery date
        new Priority(Priority::NORMAL)       // some priority, from 1 to 9
    );
```

One thing to be aware of in case you want to dispatch both Synchronous and Asynchronous messages is that you need to
make sure the `AsynchronousDeliveryMiddleware` in the bus is called *before* the `SynchronousDeliveryMiddleware`. 
Otherwise the Synchronous middleware will always handle it before it can be queued, and queueing will not work.


## Installation

To install the library, run the command below and you will get the latest version:

```
composer require werkspot/message-bus
```

## Tests

To execute the tests run:
```bash
make test
```

## Coverage

To generate the test coverage run:
```bash
make test-with-coverage
```

## Code standards

To fix the code standards run:
```bash
make cs-fix
```
