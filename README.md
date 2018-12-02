the_movie
=========

A Symfony project created on November 30, 2018, 5:02 pm.

Instructions For Installation and Utilization:

1) Clone the repository.
2) Go into the ToMovie Directory
3) run 'composer install' (when prompted for the database user and password, you may need to change it to the relevant user/password that your Mysql database corresponds to. The database name can remain as is)
4) at the command line, enter 'php bin/console doctrine:database:create' - doctrine will create the database known as symfony'
5) type in 'php bin/console server:run'
6) In a browser, plug in localhost:8000 and peruse the program
	- You should see a link 'Select Movie'. Click on it.
	- You will be redirected to /toMovie/. This page will show you a list of all the movies you currently own.
	- Enter in the name of a movie in the input field and hit enter to access the To Movie DB.
	- You'll be taken to a page displaying whatever the API returned. You should see only 10 results with the overall result number at the bottom. You will see the title, release date and an overview.
	- Notice the checkboxes. They will be checked if you currently own the movie and unchecked if not. Here is where you will select the movies you claim to own or no longer own. Once the selection is made, hit submit.
	- You should now be redirected to /toMovie/ and the movies you now own or no longer own should be displayed here. You will only see the title
	- To add more movies, make another selection and hit 'Get Movies'. To remove them, search for the movie and if you already own it, uncheck the checkbox when you see it in the search and submit the form.
