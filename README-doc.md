# Earnie Platform Introduction

Earnie is a new product devised by Lightfoot. It takes the ideology of rewarding people for positive performance to a whole new level, one which we can sell to companies and businesses to reward their own employees and customers.

In general the purpose of the platform is that end-users can enter competitions and lotteries and have a chance to win prizes if their **Score** is past a certain level. Importantly, the score type and the way the score is calculated will change from instance to instance of the Earnie application (see Configuration). So for example, one platform could give users a `%` score that's averaged over a time period, and in order to be eligible to enter a competition you'd need `>= 85%` score for the `last week`. Another server might be a score of `True` or `False`, and only if you've averaged more `True` than `False` over the `last month` are you eligble to enter a Lottery. So the Score system will be incredibly dynamic which adds complexity to the build. More information in the Build Section.

### Contents

{{TOC}}

### Some definitions

Before we begin, here are some definitions:

1. **Customer/Provider**: This will be the company that Lightfoot sells this platform to. They want it because they can use the platform to incentivise their own customers or employees and reward their good behaviour. They will also provide the **Score** data for their users.
2. **Lightfoot**: In this context, this is the company who owns the Earnie platform and will sell it to customers. They will also use this platform for their own users.
3. **Users**: Users are the people who get to win competitions and enter lotteries. Their data will be put into the system by the *Provider*. Most likely they will not know that this product has been created by *Lightfoot*.

# Overview

Earnie is an evolution of the current Lightfoot product. While in Lightfoot, users get rewarded with Competitions and Deals for driving well (Score of >= 85%), in this platform, users can get rewarded in exactly the same way but based on whatever metric the *Provider* of the platform desires. This could be a score as a percentage, or as a true/false value or just as an integer that accumulates (there are quite a few possibilities - see Configuration). This will be set up on a case by case basis using our platform. We'll do this by having replicas of our platform that we can configure in different ways for the different needs of Earnie Customers.

This is one of the core pillars of this development work packet, and that is using configuration files and containers to make entirely generic and replicatable instances of the application for different customers. This means that we have to build everything in such a way that from **one codebase** we can **provide for different use cases of customers**. This makes things much more complex as our platform has to be completely **generic**.

The general layout will be as shown in the below diagram. 

![Architecture 1](/readme-images/Architecture - single deployment.png)

The Earnie app consists of the Web Server which is generic and can be configured for different Customers, and a Mobile Front End which is also generic and can be branded for each Customer's needs. The Mobile App will be developed at a later date. The Earnie web server consists of 2 (potentially 3) interfaces.

1. **Management API**: Allows the provider to manage the following:
	- Users (Create/Update/Delete)
	- Competitions/Lotteries (Add/Update/Delete)
	- Leagues (Create/Update/Delete)
	- Groups (Create/Update/Delete)
	- Rewards (Create/Update/Delete)
	- Data Input (Create) - potentially the 3rd interface
2. **Front End API**: Allows users to login and interact with Earnie in the following ways:
	- Login
	- Register
	- Competitions (List/Join)
	- Lotteries (List/Join)
	- Rewards (List)
	- Leagues (Create/List/Join)
	- Scores (List)
	- Profile (List/Edit)

Unfortunately there is a huge amount of business logic that goes into each of these, and these will have to be detailed on a task by task basis as we develop the platform.

Below is an example of the different Use Cases of the app. Note that Lightfoot wants to use it for itself, but in a slightly different way where the Lightfoot servers sit in between the current Lightfoot mobile app and the Earnie server, meaning they won't be using a separate, generic mobile app as per the other Use Cases.

![Cluster architecture](/readme-images/Architecture - Cluster.png)

Through different configuration files we will provide different options to each instance of the Earnie server. These configurations will cover the following:

- Provider Branding
- Data Type (Score) Management - essentially the core of the entire platform.
- Flags
- Access config

And more…

Once a Provider has their own configured server, they will use the Management Interface to upload and manage users, competitions, leagues and more. But more importantly, they can start sending Key Performance Indicator (KPI) data for their users. Once it's stored in our database, the KPI data will be aggregated based on the configuration and used as a Score. This score is central to the platform. It will often be calculated on-the-fly based on the configuration of the platform and may also be used against a Target on competitions and Lotteries to determine if the user can win the competition/lottery. It'll also be used in Leagues to rank the users.

# Technologies and Basic Functionality

## Laravel

We're going to be using Laravel to build this application. Please make yourself familiar with all the features available to us with Laravel and always think how we can use Laravel functionality to achieve our goals. Their docs are really thorough, so [please read them before beginning](https://laravel.com/docs).

### Structure

We should stick to the same, widely used structure that is common with Laravel when developing this application. It's incredibly important that we keep a very strong MVC pattern, and expand upon it even further.

Please read up on the [directory structure here](https://laravel.com/docs/5.8/structure) and ensure that we're using all of Laravel's features correctly, whether it's using their Mail system (in the `app/mail` directory), `middleware` in (`app/http`) or `database migrations` (in `database` directory). We must stick to all the Laravel standards where applicable.

In general, all middleware and controller logic goes into the `app/Http` folder (or `app/Console` if you're creating access points from the command line). It's important that **absolutely no Business logic is performed here**. 

More complex programming (or Business Logic) which covers the bulk of our application logic should be stored in a folder called `app/models` folder or in the `ORM class` if the logic is acting upon an object, see below.

#### ORM

We should be using the [Eloquent ORM](https://laravel.com/docs/5.8/eloquent) for objects where appropriate. Examples include:

- Competitions
- Leagues
- Groups

And any other appropriate objects. ORMs should wrap business logic relating to an object in that class, as well as provide a way for managing the Database schema and accessing the DB for that object.

Essentially every object we create should have `metadata`, this will help our application be flexible for different use cases.

#### DB schema migration

The entire Database Schema should be managed by our application. We should never be going into MySQL and manually adding in Schema changes. The ORM will do some of this for us but only for tables relating to objects. For everything else you'll need to use the [DB migrations](https://laravel.com/docs/5.8/migrations).

We should never break backwards compatability with our database schema. This means that v1.0 of the application should work perfectly fine with a database upgraded by v2.0 of the application. We can do this by ensuring a couple of things:

1. We should never change the name of or remove an existing table.
2. We should never change the name of or remove an existing column.

Stick to this will ensure that our schemas updates do not break previous versions of the application.

#### User authentication and role-based access

We should use either a Laravel feature or a library for Laravel to manage users in the platform. Users should have roles associated with them so we can decide (likely using `middleware`) whether they have access to the `Management APIs` or the `Frontend APIs`. On top of that, authenticating with the API itself should use a Laravel library.

## Docker

We should be using Docker and Docker-compose for development. This is a really important thing to get right, and there are many tutorials available for setting up Laravel in a Docker container. The development environment should be built using Docker-compose and that should include any dependencies such as the Database. Setting up the dev environment should be as simple as `docker-compose up`.

## Configuration

Configuration should be managed using **Environment Variables** in our **containers**, so that we can dynamically change the settings from container to container, and from customer to customer. The Data Type management is possibly the most important thing. This will be taken into account in all calculations where a "Score" is required to be displayed as well as validating when a KPI Target is met. For instance, you can only be eligible to win a Lottery when you're score is better than the KPI Target for that Lottery.

```
- Branding
	- Basic branding of the provider inc Image, Name, Colour scheme
- Data Type (Score) Management - essentially the core of the entire platform.
	- Data Type
		- Fraction
		- Integer
		- Bool (requires names for 1 & 0 values)
	- Aggregation Method
		- Weighted Average (Default to 1 if no weight sent)
		- Sum
		- Mode
		- Last Value
	- Aggregation Base Period
		- Weekly
		- Monthly
		- Daily
	- Unit
		- Symbol (e.g. %, pass/fail etc. etc. optional)
		- Prefix/Suffix (optional)
	- Default Score Target
		- `>`, `<`, `>=`, `<=`, `=`
		- Value
- Flags
	- Invite Only Registration
- Access config (Some providers may not have access to certain features)
	- Disable access to features
```

## Unit testing

All our business logic in it's multiple locations should be unit tested. Again, there are good guidelines for doing this in the [Laravel testing docs](https://laravel.com/docs/5.8/testing) and there are many tools available to help you test things (e.g. [Seeds](https://laravel.com/docs/5.8/seeding) for populating the DB with test data) again, highlighting the importance of reading the [entire Laravel documentation](https://laravel.com/docs).

We are aiming for a unit test coverage of 75%, which means 75% of the lines of code in the application should have at least 1 unit test covering them. We should *aim* for 100% in models.

In general your development flow should go as follows:

1. Understand requirement.
2. Build model functions.
3. Build test functions for new model functions.
	4. Inserting known, reliable test data into the DB where appropriate.
	5. Using mocks where you don't want real code to execute.
	6. Testing as small an amount of code as possible.
	7. Covering as many possibilities as possible.
4. Run tests.
5. Refactor and repeat if necessary.
6. Commit code.


## Api development and documentation

The API is so important to get right. It's what's holding together the entire system, whether it's the Management API (For plugging in data and managing the platform) or the Frontend API (for connecting with a mobile app). Because of that, there are two things that must be done for our APIs, documentation and versioning.

### Documentation

It's important that every API we create is properly documented. We can use Postman for this (it also provides a means of testing the APIs too) or we can use something that's more closely built into Laravel. Whichever we choose. *Every* API Endpoint must be documented clearly and thoroughly so other teams can use it. We should not be publishing code without updated documentation.

### Versioning

On top of documenting everything, we must also ensure that after we release our first version for consumption, every change to the API is either non-breaking or in a new version. We should use minor (0.`x`.`y`) and major (`x`.0) version numbers to represent this - where `major` versions _**aren't**_ compatible with previous versions and `minor`  versions **_are_** backwards compatible. These versions will then be tagged against Docker Containers through a process that we will have to define once we begin.

Things that break backwards compatability are:

- Changing existing endpoint names.
- Changing existing keys/response formats in endpoints.
- Removing endpoints.
- Removing keys/response formats.
- Removing features.

And anything else that will cause something that consumes the API to break.

## Localisation

We should be building this platform so that every single string is loaded from a localisation file which we can then get translated. This includes all error messages and responses from both APIs. Please see [the Laravel documentation](https://laravel.com/docs/5.8/localization) for more information.

We should be using variables and pluralisation in our strings too. 

## Functionality

In this section you'll find a very brief list of functions, there will be a lot more work than meets the eye here as there is a lot of Business Logic that we will add as we go as there is too much to detail.


### User Management

Users will generally be managed through the management interface by the partner. However, sometimes, the user will register themselves.

#### Backend API

This will allow users to be managed through the management interface.

- CRUD (also bulk import and send emails)

#### Frontend API

Users will generally register using an invite code that is generated through the management interface. This would create the user and place them into a pre-determined group.

Sometimes, open registrations will be permitted, where any user can register without a code.

- Register with invite code
- Register normally (if allowed)


### Groups

A group hierarchy is used to manage users, by assigning each user to a single group in the hierarchy. These group dictate the visibility of public leagues, group leagues, competitions, and lotteries to the user. This visibility flows down this hierarchy such that all users can see a public league created at the top of the hierarchy.

#### Backend API

Allows the group hierarchy to be managed through the management interface and for users to be assigned in bulk. Users will generally be added at the time of creation.

- CRUD
- Assign users in bulk


### Data Management (KPI submission)

#### Backend API

This will allow user KPI data to be uploaded for each user. The KPI data will consist of timestamped values for each user.

- Add KPI data for a user.


### Score

All users will have a score, determined by aggregating the KPI submissions (see aggregation methods in the Data Type section). 

For example, if the aggregation method is ‘sum’ and the following data submitted:
22/07/2019 = 10
25/07/2019 = 12
26/07/2019 = 1
then the score would equal 23 for the week 22/07/2019 to 28/07/2019.

#### Frontend API

This will provide the user frontend with score data to show in the app. Typically, it will need to provide the aggregated score for a time window. For example, the user’s score for last week.

- Get score data


### Leagues

It is possible for each user to compare their score against other users’ by entering and viewing leagues. Each league consists of a list of users and their scores.

The time period spanned by all leagues is set by the system constant ‘Aggregation Base Period’. For example, in a system where ‘Aggregation Base Period’ = week, the scores shown in the leagues will be each users’ aggregated week score.

Leagues consist of public and group leagues, which can be created through the management interface (users cannot edit these), and private leagues, which are managed by the users themselves. 

#### Backend API

This allows leagues to be created through the management interface. There are two types of league that can be created through this interface: A) Public leagues, which users can view (if above the user in the group hierarchy) and enter if they want to, or; B) Group leagues, which are linked to a group and users in that group are automatically entered into.

- CRUD
- Manage users for some leagues (not all)

#### Frontend API

This allows users to create their own leagues through the frontend. They can invite other users into the league using their email address or screen name. These private leagues are independent of the group hierarchy so any user can access them.

- Get
- Join
- Invite
- Shutdown
- Create


### Competitions

Competitions are one of the core user features, which allow users with a chance to win a prize by exceeding the KPI target for the competition period. Users may enter the competition whilst it is running (if it is visible to them through the group hierarchy), then the winners are automatically selected when the competition closes.

Each competition runs for a period determined by the competition duration. For example, a competition with ‘Duration’ = week and start date ‘22/07/2019 would run from 22/07/2019 to 28/07/2019, and winners are then awarded on 29/07/2019. The competition ‘notification’ configuration determines whether a notification of competition closure is sent to entrants or just the winners. 

Prize types consist of: cash, goods, and digital download (vouchers). Winners can automatically claim cash by adding it to their account, whereas goods and digital downloads are listed in the management portal so they can be managed offline.

Some special competition types exist:
1)	Fixed number of spaces = There is a limit to the number of entries permitted.
2)	Entry fee = The user must pay and entry fee from their account to enter
3)	Rolling duration = The competition automatically renews after the end date with users automatically re-entered, depending on settings.

#### Backend API

This allows competitions and prizes to be setup and managed through the management interface. Entrant (winner) management allows management users to view the awarded prizes and to mark them as shipped when they have been dispatched offline.

- CRUD
- Manage Prizes
- Manage Entrants

#### Frontend API

This allows users to view and enter competitions through the user frontend. Additionally, it allows a banner to be shown on the user frontend if they have won a prize, then for the user to claim that prize by providing any missing account information (delivery address etc).

- Get
- Join
- Win


### Withdrawing Cash

Each user will maintain a cash balance, earnt from winning cash competitions. It will be possible for the user to withdraw this cash balance to a bank account. In the future, it will be possible for the user to spend the balance on buying goods.

#### Frontend API

- Withdraw cash


Attached is a mindmap that highlights the general structure of the application.

![Mindmap](/readme-images/[Shortened] Generic Lottery Platform Mind Map.png)

