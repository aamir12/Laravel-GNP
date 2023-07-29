# Deploying locally with Docker

## Windows
#### Start local environment:
1. `dos2unix start.sh`
2. `docker compose -f docker-compose.yml -f docker-compose-local-setup.yml build`
3. `docker compose -f docker-compose.yml -f docker-compose-local-setup.yml up`

#### Stop docker containers:
1. `docker compose -f docker-compose.yml -f docker-compose-local-setup.yml down`

## Ubuntu or macOS
#### Start local environment:
1. `docker compose -f docker-compose.yml -f docker-compose-local-setup.yml build`
2. `docker compose -f docker-compose.yml -f docker-compose-local-setup.yml up`


#### Stop docker containers:
1. `docker compose -f docker-compose.yml -f docker-compose-local-setup.yml down`

# Environment Variables
For the API to function properly, a number of environment variables must be set:

## KPI Config
#### `KPI_DATA_TYPE`
This setting lets the app know what kinds of values to expect as KPI data. It can be one of three values:
Possible values are:
- **`integer`** - Float values can still be processed but will be rounded to the nearest integer.
- **`fraction`** - Float values rounded to 2 decimal places.
- **`bool`** - Bool values should be set as 1 or 0.

#### `KPI_AGGREGATION_METHOD`
This setting determines how user’s scores will be calculated from KPI data.
Possible values are:

- **`weightedaverage`** - Score will be calculated by multiplying KPI values by their associated weights, and then finding the mean of the results.
- **`sum`** - Score will be calculated by summing KPI values together.
- **`mode`** - Score will be calculated by finding the most commonly occurring KPI value.
- **`last_value`** - Score will be equal to the KPI value with the most recent timestamp.

Please note that certain aggregation methods will not work with some data types. Therefore the following combinations of data types and aggregation methods are not valid:

- **`bool` & `weightedaverage`**
- **`bool` & `sum`**

#### `KPI_BASE_PERIOD`
This setting determines the timeframe within which KPI values will be taken into account for calculating user’s scores.
Possible values are:

- **`daily`**
- **`weekly`**
- **`monthly`**

Note that these base periods start from the beginning of that period, i.e. monthly will be from the 1st of the month and **NOT** from 30 days ago. Likewise, weekly will be from Monday at 00:00 and **NOT** from 7 days ago.

## Email Config
#### `MAIL_USERNAME`
The address of the email account used by the app to send emails.

#### `MAIL_PASSWORD`
The password of the email account used by the app to send emails.

#### `ACCOUNTING_MAIL_ADDRESS`
The email address that balance withdrawal requests will be sent to for processing.

## Auth Config
The following environment variables must be set to configure authentication.

#### `OPEN_REGISTRATION_ALLOWED`
Must be set to a  `true` or `false` value. This setting determines whether or not users are allowed to register without having first received an invitation.

#### `PASSWORD_EXPIRE_SECURITY`
Must be set to a  `true` or `false` value. This setting determines whether passwords will expire and need to be changed at regular intervals. The expiration time is determined by the value of `EXPIRE_PASSWORD_IN_DAYS`.

#### `EXPIRE_PASSWORD_IN_DAYS`
Must be an integer value. This setting determines how many days a password remains valid for before it is considered expired and must be changed. This setting only has an effect if `PASSWORD_EXPIRE_SECURITY` is set to `true`.

#### `LOGIN_ATTEMPTS_BEFORE_LOCK`

Must be an integer value. This determines the number of failed login attempts a user may make before their account is locked.

#### `PASSWORD_LOCK_TIME_IN_MINUTES`

Must be an integer value. This determines how many minutes a users account will remain locked for after exceeding the maximum number of failed login attempts.

#### `PASSWORD_VALIDATION_MIN`

Must be an integer value. Determines the minimum length allowed for passwords.

#### `PASSWORD_VALIDATION_MAX`

Must be an integer value. Determines the maximum length allowed for passwords.

#### `PASSWORD_REQUIRES_LOWERCASE_CHAR`

Must be a boolean value. If true, password validation will enforce that passwords must contain at least one lowercase character.

#### `PASSWORD_REQUIRES_UPPERCASE_CHAR`

Must be a boolean value. If true, password validation will enforce that passwords must contain at least one uppercase character.

#### `PASSWORD_REQUIRES_NUMBER`

Must be a boolean value. If true, password validation will enforce that passwords must contain at least one numeric character.

#### `PASSWORD_REQUIRES_SYMBOL`

Must be a boolean value. If true, password validation will enforce that passwords must contain at least one special character.

## Deeplink Config

#### `IOS_DEEPLINK`
This is required to enable deeplinking from app generated emails directly into the iOS app (or to the AppStore if the app is not installed on the user's device).

#### `ANDROID_DEEPLINK`
This is required to enable deeplinking from app generated emails directly into the Android app (or to the PlayStore if the app is not installed on the user's device).

## Feeder World Config

#### `IS_FEEDER`
Must be a boolean value. This setting determines whether or not this instance is a Feeder World. This setting will drastically change the behaviour of the app and should only be used when the app is being deployed alongside a Global World.

#### `KPI_DESTRUCTIVE_UPDATE`
Must be a boolean value. This setting determines whether or not KPI data will be retained after it is exported to the Global World. If set to true, exported KPI data will removed from the database and archived as SQL dump files.