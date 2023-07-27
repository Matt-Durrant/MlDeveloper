# ML DeveloperTest
This module allows the store owner to block specific countries from being able to purchase certain products.

## Configuration
From the `Admin Panel`, go to `Stores > Configuration > ML DeveloperTest > Geo IP`, choose `Geo IP Settings` section.
- **Enable Module**, please select  `Enable`  to turn on the modules function of blocking specific countries from purchasing certain products
- **API Url**, Enter the API url of the geo IP service you wish you use. I recommend using https://ip2country.info/ (https://ipapi.com/)
- **API Key**, please select  `Enable`  to turn on the modules function
- **Test API**, Once you have entered the API Url and API key you can click the `Test API` button. This will make a request to the geo IP service using the details you have entered and your IP address.
- **Custom Error Message**, Enter the error message that you wish to display to the user if their country is blocked from purchasing a certain product

## Features

At product level an admin can choose which countries they want to block from purchasing the product. They can mutli select from a list of countries using the `Block country from purchase` attribute on the product.

When a visitor tries to add a product to their cart the module will first check if the module has been enabled in the admin config. It will then use the visitors IP address and make an API call to the configured API url with the API Key. From the results it will then check if the visitors country code is within the list of blocked country codes for that product. If it is then the module will prevent the product from being added to the customers cart and display the configured error message.