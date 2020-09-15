# imagine
Image resizer for object storage.

This is a script that takes a feed id and an image url as arguments on the command line, resizes the image to 2400px, 1200px, 600px and 300px, then moves them to object storage (minio expected) /data location.  It expects the bucket folder to be /feed.

Each object url is appended with a unix timestamp. If the timestamp changed, the image has changed.


## Example

This would be a typical API response with feed metadata:  (https://raw.githubusercontent.com/Podcastindex-org/imagine/master/example_feed.json)

The original image given in the feed is referenced as "imageOriginalUrl".

Depending on which versions we were able to resize, the resized versions are added to the API response as "imageResizedXXXX" where XXXX is the pixel width of the resized image.300px


## Command Line

The script is run like:  

`php ./imagine.php [feed id] "[image url]"`

So, something like this:

`php ./imagine.php 75075 "https://www.theincomparable.com/imgs/logos/logo-batmanuniversity-3x.jpg?cache-buster=2019-06-11"`

That would give you these outputs:

- https://images.podcastindex.org/feed/75075/2400.jpg?ts=1600189122
- https://images.podcastindex.org/feed/75075/1200.jpg?ts=1600189122
- https://images.podcastindex.org/feed/75075/600.jpg?ts=1600189122
- https://images.podcastindex.org/feed/75075/300.jpg?ts=1600189122

The resized files are moved to "/data/feed/[id]", which is where a minio server is expected to be running with "/data" as it's bucket volume.
