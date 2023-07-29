# Global Earnie Technical Overview

The Earnie platform has been expanded to allow multiple instances of the Earnie application to be deployed in tandem and for those instances to be able to communicate with each other to allow for more sophisticated functionality.

The existing Earnie infrastructure did already allow for multiple instances to be deployed, however these instances were entirely independent of each other with no means of sharing data. The main goal of the new configuration was to unify the Earnie platform to make the sharing of data between instances possible.

**Note**: The new infrastructure is entirely optional. It is still completely possible to deploy an Earnie instance in an isolated space so that it is not to sharing any data with, nor even aware of other instances. Additional instances can be added on demand.

## Contents

{{TOC}}

## Definitions

Before we begin, here are some definitions:

- **Admin Users**: The users who interact with the Earnie Management Portal app. These are the users who create and manage competitions, prizes, leagues, and most importantly, invite **End Users** to the Earnie platform.

- **End Users**: These are the users interacting with the iOS and Android apps. They participate in competitions/lotteries/leagues and it is their KPI data which is entered into the Earnie system.
- **Earnie World**: An "Earnie World" is simply a term to refer to any given deployment of the following 3 services:
  - Earnie API
  - Earnie Database
  - Earnie Management Portal

# Infrastructure

## TL;DR

- **Global World** - 1 instance. Serves as central Authentication Server and receives KPI Data from Feeder Worlds.
- **Feeder World** - 0 or more instances. Periodically aggregates KPI data and sends results to Global World.
- **API Gateway** - 1 instance. Acts as the middle-man between end (non-admin) users and Earnie worlds.

## A bit more information

- **Global World** - In any given deployment there will always be one special Earnie World. This is known as the "Global World". This instance fulfils a number of special roles including serving as a central authentication server and receiving KPI data from Feeder Worlds as well as also functioning as a regular Earnie instance with its own admin API and management interface. [More information on the Global World here](#global-world).
- **Feeder World** - A Feeder World is simply another Earnie World, just like the Global World, but with certain features toggled so that it behaves slightly differently. Unlike the Global World, there can be many Feeder Worlds deployed in tandem. The primary role of Feeder Worlds is to periodically aggregate and *feed* (hence the name) KPI data to the Global World. [More information on Feeder Worlds here](#feeder-world).
- **API Gateway** - The API Gateway is an extra layer between End Users and Earnie Worlds. The API Gateway can authenticate incoming requests since it is able to verify existing tokens, however it cannot generate valid tokens and hence the Global World is remains the ultimate source of authenticaion. All End User requests must go through the API Gateway where they are first authenticated before being forwarded to the desired World. [More information on the API Gateway here](#api-gateway).





# Global World





# Feeder World





# API Gateway

This is a Lumen application (Lumen is essentially a slimmed down version of Laravel).



# Configuration

Earnie Worlds and the API Gateway are configured using environment variables.



There are a number of places throughout the application where the `APP_IS_FEEDER` flag has been used to turn on or off particular features. For example, when `APP_IS_FEEDER` is "true", invitation emails will use a different template than that used for a world where `APP_IS_FEEDER` is "false". In this way we can easily differentiate the behaviour of Global and Feeder Worlds.