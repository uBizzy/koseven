# Creating a New Application

[!!] The following examples assume that your web server is already set up, and you are going to create a new application at <http://localhost/gitorial/>.

Using your console, change to the empty directory `gitorial` and run `git init`. This will create the bare structure for a new git repository.

Next, we will clone koseven Go to <http://github.com/koseven/koseven> and copy the "Clone URL":

Now use the URL to initialize koseven:

    git clone git://github.com/koseven/koseven.git .

[!!] This will download the current development version of the next stable release. 
Please Note: The development version is not intended to be used on production servers.
To switch to `master` branch which is stable, simply run:

    git checkout master

Now that we are in master, you can commit those files to your repository:

    git commit -m 'Initial commit'

We don't want git to track log or cache files, so add a `.gitignore` file to each of the directories. This will ignore all non-hidden files:

    echo '[^.]*' > application/{logs,cache}/.gitignore

[!!] Git ignores empty directories, so adding a `.gitignore` file also makes sure that git will track the directory, but not the files within it.

That's all there is to it. You now have an application that is using Git for versioning.
