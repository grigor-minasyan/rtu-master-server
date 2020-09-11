# Remore telemetry unit (RTU) Master server

This project was done as my main project for a bootcamp at a company whose focus is to create remote monitoring solutions for companies with custom needs. 

Here is a video of showing the overview of the funcitons of the Master Station
https://www.youtube.com/watch?v=sAqVrAxL70c

This repo is half of the whole project. The code in this repo is the web interface of the master station as well as the php files to communicate with the back end. Here are the main features and operations supported by this web server.

* Login / Register / Reset passowrd page. Works with PHP and MySQL database.
* Deisgned to be a one-page simple design that shows all of the data in an informative fashion.
* Can support multiple RTU, add / remove RTUs from the web interface and the database will be updated.
  * The Python program (separate repo) will get updated too and poll the updated RTUs from the MySQL database.
* Support fro removing individual events from the webpage. The database will be updated automatically.
