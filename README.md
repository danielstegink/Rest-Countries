# Rest-Countries
API that allows the user to build a list of countries

Instructions

To run this project, place each of the attached files in the appropriate folder. I used XAMPP to run my PHP, so I put them all in the htdocs folder, but if you use something else to run PHP then you should place the files in the folder that your implementation of PHP looks at for HTML and PHP files to build and run.

Next, after you start your local server, enter "localhost/restCountries.html" into the URL of your web browser. This will display the initial form. Enter the name or code of the country or countries you wish to examine, and click Submit. The error message at the bottom should be replaced with a list of countries matching the format described above. If no countries match your search, the default error message should be displayed, instead.

How it works

This project has 3 parts split up across 2 files.

The first part is the HTML form in restCountries.html. This is simply the HTML form that takes in user input in the Country Name and Country Code fields, and the country_list div that holds the displayed information.

The second part is the script tags contained in restCountries.html. These utilize Javascript and JQuery to, on a click of the Submit button, take in the inputted values for country name and code, pass the values to PHP in order to get some JSON-encoded data, and then tries to use that data to display a list of countries. After getting the data from PHP through an Ajax call, the script code will either build the default error message, "There are no countries to display that match the current input.", or it will build a list of countries. This code is contained in a try-catch block to handle any issues that PHP encounters. The list of countries will take the form of a numbered list and a bulleted list. The numbered list will display each country in alphabetical order. Within each point will be a country entry, which will have each of its properties displayed in a bulleted list. Each country's flag is formatted so its width will be about a 1/4 of the screen. Next, there will be a bulleted list to display the total number of countries found (not the total displayed), as well as which regions and subregions were found and how many times each were found. The list of regions, subregions, and languages will all be nested bulleted lists.

The third part is the PHP code found in getCountries.php. The PHP code first gets the name and code variables that were passed in by the script code, and then tries to query restCountries.eu to get a list of valid countries. Once the data is gathered, the string is then split up using keywords to examine each country individually. Once each relevant property has been stored in a local variable, the region and subregion lists are incremented to reflect that another region and subregion were found during the search. Then, the country's properties are stored in a single object, which is stored in an array of country objects. Once all of the countries are accounted for, the total number of countries found is stored and the countries are sorted by name and population. From there, the first 50 are sliced out and kept. Lastly, each of the values we've gathered (countries, regions, subregions, and total countries) are placed in an object, encoded by JSON, and returned to the script tag.
