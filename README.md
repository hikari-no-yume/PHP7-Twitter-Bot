# PHP7-Twitter-Bot

Simple Twitter bot framework for PHP 7. Add `"hikari_no_yume/twitterbot"` to your Composer dependencies.

## Usage

Create a class which implements `hikari_no_yume\TwitterBot\TweetDeviser`. This just needs a single `string`-returning `devise` method. For example:

```PHP
class MyTwitterBot implements TweetDeviser {
    public function devise(): string {
        return "Hello, world!";
    }
}
```

This is the heart of your bot. Whenever a tweet is to be made, `devise()` will be called and the returned string will be tweeted.

Then you just need to write a short script that makes a `hikari_no_yume\TwitterBot\Tweeter` object, passes it your `TweetDeviser`, sets whatever configuration parameters are appropriate, and sets it going:

```PHP
<?php declare(strict_types=1);

use hikari_no_yume\TwitterBot\Tweeter;

require_once __DIR__ . '/vendor/autoload.php';

$tweeter = new Tweeter(new MyTwitterBot);

// Loads Twitter oauth_access_token, oauth_access_token_secret, consumer_key and consumer_key from a JSON file
// You can also get these from somewhere else and use $tweeter->setTwitterKeys($object)
$tweeter->loadTwitterKeysFromJSONFile("settings.json");

// Optional. Sets the interval between tweets, in minutes. The default is 30.
$tweeter->setTweetInterval(30);

// Optional. Sets how long to wait before retrying devising a tweet (if it threw a Throwable), in seconds. The default is 60.
$tweeter->setDeviseRetryInterval(60);

// Optional. Sets how long to wait before retrying sending a tweet (if it failed), in seconds. The default is 60.
$tweeter->setTweetRetryInterval(60);

// Runs the bot forever.
$tweeter->run();
```

Congratulations, you now have a Twitter bot!

## License

Copyright © 2017 Andrea Faulds.

PHP7-Twitter-Bot is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

PHP7-Twitter-Bot is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with PHP7-Twitter-Bot.  If not, see <http://www.gnu.org/licenses/>.
