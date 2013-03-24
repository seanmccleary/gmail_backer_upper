# Gmail Backer Upper #

**NOTE: Since I wrote this, Drop Box and Google Drive and a number of other similar sites have rendered this pretty much useless.  I'm just leaving it here to further gum up the internet.**

The Gmail Backer Upper is a command line PHP script which sends files to the Gmail account of your choice. It's intended to be run as a scheduled job, for small projects which need regular backups, but without the time, resources or need for a more sophisticated backup solution. I whipped it up because I needed it for a project.

You could, for instance, set it up with a line like the following in your crontab:


```
#!sh
php gmail_backer_upper.php /nightly_db_dumps/*
```

## Whoa! Won't my mailbox eventually fill up? ##

Depends on how much you send to it and how often you delete it. If you set up a new Gmail account just for your backups, you could also make a filter to automatically throw all backup emails into the trash, where they'll automatically be deleted after 30 days. At Google's current limit of 7 gigs, that's about 230 megs a day you could back up.

I never said this was some kind of *fancy, hi-fi* backup solution.

## What Do I Need? ##

Well, a Gmail account to which you can send the backup emails, of course.

It also relies on the PEAR Mail, Mail_Mime, and Net_SMTP packages.

## License ##

Gmail Backer Upper is licensed under the [CC0 1.0 Universal Public Domain Dedication](http://creativecommons.org/publicdomain/zero/1.0/).

To the extent possible under law, Sean McCleary has waived all copyright and related or neighboring rights to Gmail Backer Upper. This work is published from: United States.

However, if you do make changes, a patch sure would be civilized. You can contact me here.

And you know what? Drop me a line if you do use it. I'd just like to know. It'd make me sleep better at night.
