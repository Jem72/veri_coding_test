**Assumptions**
1. Currency is in euro (or something with units and cents). This assumption is used to justify a crude representation 
of the payment as a float value rounded to 2 decimal places. This would need to be fixed for a production implementation
2. All dates and times are in the timezone of the machine on which the code is running. This could lead to some payment
errors on the users birthday if the DOB is in a different timezone.
3. The rules on how the Fuel payment is paid are unclear. I've assumed that these are similar to the travel payment and
that the payment is made if and only if employee is attending the office

**Installation**
1. After updating the code, run composer update to install any dependencies

**Running**
1. Execute the code from the root using "*php src/index.php*"
2. Run the tests from the test directory using "*../vendor/bin/phpunit .*"
3. Logged data is in log/coding-test.log. If this file hasn't been created check the directory permissions

**Docker**
1. Build by running "*docker-compose build*" in the root directory
2. Start by running "*docker-compose up -d*" in the root directory
3. The container is in tty mode so you can log in using "*docker exec -it veri_test bash*"
4. The working directory on the docker container is "*/etc/veri_coding_test*"

**Output**
1. Log file data is saved to "*log/coding-test.log*". If the process does not have permission to access this directory, 
it carries on with no logging and no warning is given. 