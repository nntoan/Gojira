GoJIRA, a PHP client for JIRA
================================================

GoJIRA is a PHP client for JIRA based on [Symfony2][1] components:

Gojira works with PHP 5.5.9 or later and is heavily inspired by the [Silex][2]
web micro-framework by Fabien Potencier.

## Why this name?

From JIRA team, I think it's funny :)

> Like all good names in the software industry, it started as an in-house code name.

> We originally used Bugzilla for bug tracking and the developers in the office started calling it by the Japanese name for Godzilla, Gojira (the original black-and-white Japanese Godzilla films are also office favourites). As we developed our own bug tracker, and then it became an issue tracker, the name stuck, but the Go got dropped - hence JIRA!

> Further investigation into the name has revealed that Gorira is Japanese for "gorilla", whilst Kujira is Japanese for "whale". So Gojira is roughly translated to mean "gorilla the size of a whale"! (Thanks to yusuke_arclamp — Oct 2002)
  
> For those who care - it sounds best if you yell it loudly, as though charging into battle. C'mon - try it!
  
 

## Installation

 1. Downloads latest [GoJIRA][3].
 (`wget -O /usr/local/bin/jira https://gojira.nntoan.com/get/gojira.phar`)
 2. Make it executable `chmod +x /usr/local/bin/jira`

<!--
## More Information

Read the [documentation][4] for more information.
-->

## Usage

##### First use

    $ jira
    Jira URL: https://jira.atlassian.com/
    Username: john.doe
    Password: xxxxxxxx

    Your top secret information has been sent to our server successfully!

This save your credentials (base64 encoded) in your `$HOME/.gorira` folder.

##### Help

Usage: `jira [options] [command]`

  Commands:

    ls [options]           List my issues
    start <issue>          Start working on an issue.
    stop <issue>           Stop working on an issue.
    review <issue> [assignee] Mark issue as being reviewed [by assignee(optional)].
    done [options] <issue> Mark issue as finished.
    running                List issues in progress.
    jql <query>            Run JQL query
    search <term>          Find issues.
    assign <issue> [user]  Assign an issue to <user>. Provide only issue# to assign to me
    comment <issue> [text] Comment an issue.
    show [options] <issue> Show info about an issue
    open <issue>           Open an issue in a browser
    worklogshow <issue>        Show worklog about an issue
    worklogadd [options] <issue> <timeSpent> [comment] Log work for an issue
    create [project[-issue]] Create an issue or a sub-task
    config [options]       Change configuration
    sprint [options]       Works with sprint boards
    With no arguments, displays all rapid boards
    With -r argument, attempt to find a single rapid board and display its active sprints
    With both -r and -s arguments attempt to get a single rapidboard/ sprint and show its issues. If a single sprint board isnt found, show all matching sprint boards

  Options:

    -h, --help     output usage information
    -V, --version  output the version number

Each command have individual usage help (using --help or -h)

##### Advanced options

Checkout ```~/.gorira/config.json``` for more options.

## License

GoJIRA is licensed under the [MIT license][5].

[1]: http://symfony.com
[2]: http://silex.sensiolabs.org
[3]: http://gojira.nntoan.com/get/gojira.phar
[4]: http://gojira.nntoan.com/documentation
[5]: LICENSE
