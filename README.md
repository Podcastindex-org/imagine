# Imagine
Image resizer for object storage.

This is a script that takes a feed id and an image url as arguments on the command line, resizes the image to 2400px, 
1200px, 600px and 300px, then moves them to object storage (minio expected) /data location.  It expects the bucket 
folder to be /feed.

Each object is stored with a CRC32 hash (of the original image url) in the path, so that if the CRC32 changes, then
the original feed image changed.


## Example

This would be a typical API response with feed metadata:  

https://raw.githubusercontent.com/Podcastindex-org/imagine/master/example_feed.json

The original image given in the feed is referenced as "imageOriginalUrl".

Depending on which versions we were able to resize, the resized versions are added to the API response as 
"imageResizedXXXX" where XXXX is the pixel width of the resized image.


## Command Line

The script is run like:  

`php ./imagine.php <feed id> "<image url>"`

So, something like this:

`php ./imagine.php 75075 "https://www.theincomparable.com/imgs/logos/logo-batmanuniversity-3x.jpg?cache-buster=2019-06-11"`

That would give you these outputs:

- https://images.podcastindex.org/feed/75075/1534720231/2400.jpg
- https://images.podcastindex.org/feed/75075/1534720231/1200.jpg
- https://images.podcastindex.org/feed/75075/1534720231/600.jpg
- https://images.podcastindex.org/feed/75075/1534720231/300.jpg

The resized files are moved to "/data/feed/[id]/[crc32]", which is where a minio server is expected to be running 
with "/data" as it's bucket volume.
