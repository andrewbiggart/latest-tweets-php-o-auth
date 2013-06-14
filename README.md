latest-tweets-php-o-auth
========================

Twitters API v1.0 has now been retired. Here's a way you can pull your tweets from Twitter using PHP and OAuth.

Overview
========================

- Tweets are cached to avoid exceeding Twitter’s limit of 150 requests for a user’s RSS and json feed per hour.
- A fallback is provided in case the twitter feed fails to load. this can be edited to suit your needs.
- A configuration parameter allows you to specify how many tweets are displayed
- Dates can optionally be displayed in “Twitter style”, e.g. "12 minutes ago"
- You can edit the HTML that wraps your tweets, tweet status and meta information

Parameters
========================

- Twitter handle.
- Cache file location.
- Tweets to display.
- Ignore replies.
- Include retweets.
- Twitter style dates. ("16 hours ago")
- Custom html.

Usage
========================

Firstly you will need to register your app / website with Twitters developer site. (https://dev.twitter.com) you will then get your consumer key, consumer secret, access token and your access token secret. You then need to add them to the script.

You should edit the Twitter ID in the function call above before using the function (it appears at the very bottom of the code snippet).

You probably also want to edit the location where the twitter feed is cached – by default it is written to the root level of your domain. To change the location, modify the $cache_file variable, or pass the new location as a function parameter.

Feel free to edit any of the other parameters to suit your needs.

Notes
========================

Twitter feeds may contain UTF-8 characters. I have found that running PHP’s utf_decode method on tweets didn’t have the expected result, so my recommendation is to instead set the charset of your HTML page to UTF-8. Really we should all be doing this anyway. (http://www.w3.org/International/O-charset)

Credits
========================

I was orginally using Pixel Acres script (http://f6design.com/journal/2010/10/07/display-recent-twitter-tweets-using-php/). But since Twitter has retired API v1.0, the script no longer worked because it didn't include authentication. I have now modified the script to include authentication using API v1.1.

The hashtag/username parsing in my example is from Get Twitter Tweets (http://snipplr.com/view/16221/get-twitter-tweets/) by gripnrip (http://snipplr.com/users/gripnrip/).

My RSS parsing is based on replies in the forum discussion "embedding twitter tweets" on the Boagworld website. (http://boagworld.com/forum/comments.php?DiscussionID=4639)

The file caching is based on the AddedBytes article "Caching output in PHP". (http://www.addedbytes.com/articles/for-beginners/output-caching-for-beginners/)

Authentication with Twitter uses twitteroauth. (https://github.com/abraham/twitteroauth)

License
========================

The MIT License (MIT)

Copyright (c) 2013 Andrew Biggart

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
