Phutler
=======

Phutler could be your Butler written in PHP. Think of it as your helper that runs in the background and does everything you can implement in PHP.
The basic principle is that phutler provides a number of data-sources which can be used to gather data, and it provides a number of actions
that can be executed. A Task uses data from the data-sources and triggers actions.

Phutler is based on the excellent https://github.com/reactphp/react (node for php), so you get all the benefits of reactphp for free.


Data-Sources
------------
Data-sources provide data that tasks can work with. Example Data-sources would be data from an url, ping data (if a computer is pingable),
etc. Whatever you can imagine as useful data-source can be implemented.



Actions
-------
Actions implement what a Phutler based butler can do. These could for example be: Send an Email, Tweet Something, Speak some text via Text2Speech, etc.


Tasks
-----
Tasks combine the data from some data-sources and execute any given actions depending on the data they get from the data-sources.
A Phutler instance can execute many tasks at once.



How to use
==========
To use Phutler you should get the code, implement some Tasks, create a phutler.json config file and then run phutler using ``phutler phutler.json``.

**Note:** Phutler is not a website, it is run as a daemon in the background. Nevertheless it has a small web interface to check its status.




