<?php var_dump(exif_read_data($argv[1], 'ANY_TAG', true, true)); ?>

php -r 'var_dump(exif_read_data($argv[1], 'ANY_TAG', true, true));' photo.jpg
