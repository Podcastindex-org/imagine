# imagine
Image resizer and object storage.

This is a script that takes a feed id and an image url as arguments on the command line, resizes the image to 2400px, 1200px, 600px and 300px, then moves them to object storage (minio expected) /data location.  It expects the bucket folder to be /feed.

Each object url is appended with a unix timestamp. If the timestamp changed, the image has changed.

