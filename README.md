UNMAINTAINED
============
This project is not maintained anymore.
If you want to take over contact us at tech@cargomedia.ch.

harvest-cli
===========

Weekly Harvest reports on the console.

Installation & Configuration
----------------------------

Create a configuration file and edit to your needs:
```
cp resources/config/default.php resources/config/local.php
vi resources/config/local.php
```

Build the Docker image:
```
docker build -t cargomedia/harvest-cli:latest .
```

Run binary with Docker:
```
docker run cargomedia/harvest-cli:latest project-week
+------------+----------+----------+----------+----------+----------+----------+----------+
|            | Mon 2.6. | Tue 3.6. | Wed 4.6. | Thu 5.6. | Fri 6.6. | Sat 7.6. | Sun 8.6. |
+------------+----------+----------+----------+----------+----------+----------+----------+
| Alexis     |          |      7.5 |      8.1 |      8.8 |      3.6 |          |          |
| Chris      |      8.8 |      9.5 |      9.1 |      9.5 |      9.1 |          |          |
| Christophe |      8.9 |      7.9 |      8.1 |      1.5 |          |          |          |
| Daniel     |      5.3 |      8.8 |     11.5 |     11.7 |      3.7 |          |          |
| Fabian     |          |          |          |          |          |          |          |
| Jan        |          |      7.9 |      8.6 |      8.5 |      9.1 |          |          |
| John       |          |          |          |          |          |          |          |
| Marian     |      7.9 |      8.5 |      8.1 |      9.1 |      4.5 |          |          |
| Matteo     |          |          |          |          |          |          |          |
| Philipp    |        7 |      6.2 |      6.7 |      2.7 |      6.7 |          |          |
| Sebastien  |      6.7 |      6.9 |      6.9 |      6.4 |      6.8 |          |          |
| Timo       |          |          |          |          |          |          |          |
| Tomasz     |      4.3 |      8.1 |     12.2 |     12.2 |     11.4 |     11.7 |        ~ |
+------------+----------+----------+----------+----------+----------+----------+----------+
```

Development
-----------
Mount project volume and run with Docker:
```
docker run -v $(pwd):/opt/harvest-cli cargomedia/harvest-cli:latest project-week
```
