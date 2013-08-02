# What is Sight

Sight is an MVC framework to help with rapid development of advanced web applications.

# What it does

Sight takes a request coming in to your webserver, such as:

`http://yourdomain.com/your/imporant/file`

... and uses a regular expression such as:

`/^your\/([^\/])\/file$/`

... to match it up with a template file, which could contain something like:

1.  `<article>`
2.  `  <h1>This is your **@adjective** page</h1>`
3.  `</article>`

... which could output something like:

**This is your important page**

So in other words...

**TL;DR:** Sight allows you to capture data from requests, convert that data into something that would be useful to a template, and pass that data to the template, which gets rendered and returned to the user as their page.

# Would you like to:

*   Learn how to [install Sight](#install-instructions), or
*   Learn more about [how to use Sight](#tutorial)?
	</section>

# <a name="install-instructions">Install Instructions</a>

# Check your prereqs

*   Apache
**   with the mod_rewrite module installed and enabled, and
**   .htaccess files enabled via the AllowOverride directive
*   PHP 5.3 or later, properly configured and working with Apache

# Get the code

Currently, the only way to get the code is to retrieve it from GitHub.

1.  Go to [the GitHub page](https://github.com/spiderworm/Sight).
2.  Download the library by clicking the "Download ZIP" button.
3.  Extract the files files from the .zip.

Users experienced with Git may prefer to clone the repository instead.  That's OK too.

# Install

Copy the extracted Sight/ directory over to somewhere more permanent on your system.  I suggest **not** installing anywhere under your Apache document root, but it's up to you.

# Setting up your project

*Create your project directory under the document root (unless it's already created, such as when you're installing straight to the document root).
*Inside the Sight directory you just installed is another directory called project-template/.  Copy the contents of project-template/ to your project directory.

		<section>

# Modify your .htaccess file

The .htaccess file is an important part of the routing process.

Open the .htaccess file in your favorite text editor.  Find the two places in the file where you see `project-template/`.  Replace `project-template` with the path of your project directory relative to the Apache document root.

For example, if my project directory was at:

`<document root>/my-root/`

... and my .htaccess file was at:

`<document root>/my-root/.htaccess`

... then in the .htaccess file, I would be replacing the two spots where it says `project-template/` with `my-root/`.

# Verify

So now hopefully you'll have things up and running. Verify that by hitting your project directory via Apache in your browser. If you don't get an error of some sort, and you see the page saying "Your new Sight site is working", then you're good to go.  If you get errors, then fix your errors before moving on.

That page will contain some instructions for further verifying your installation.  Follow those instructions as well.

# Would you like to:

Learn more about [how to use Sight](#tutorial)?

# <a name="tutorial">Sight Tutorial</a>

Tutorial coming soon...

</article>