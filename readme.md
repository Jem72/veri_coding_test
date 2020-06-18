**Description**
Coding Test from James Howard for Veri. I have primarily focused on building a valid well-tested piece of code for the 
exercise. As such pretty much everything except the controller has got a unit test.   

**Assumptions & Limitations**
1. Currency is in euro (or something with units and cents). This assumption is used to justify a crude representation 
of the payment as a float value rounded to 2 decimal places. This would need to be fixed for a production implementation
2. All dates and times are in the timezone of the machine on which the code is running. This could lead to some payment
errors on the user's birthday if the DOB is in a different timezone.
3. **The rules on how the Fuel payment is paid are unclear. I've assumed that these are similar to the travel payment and
that the payment is made if and only if employee is attending the office.**
4. The code is written and tested for PHP 7.3 and PHP 7.4. It requires a minimum of PHP 7.0 but no testing has been done 
on versions prior to 7.3. I would normally write a set of environment unit tests to verify versions and required 
modules. The required modules are listed in the dockerfile and are mostly required for phpunit. I've not listed them 
separately as the docker list provides the master definition.
5. I have run the code on OSX Catalina and ubuntu 20.04. I have not run it on any windows variant
6. I have ignored security and privacy considerations entirely - storing the employee records in unencrypted CSV files is completely
unacceptable in any production environment. In addition, the code repo should be private and required keys for deployment 
should be stored elsewhere in a secure environment.  

**Installation**
1. After updating the code, run "*composer update*" to install any dependencies

**Running**
1. Execute the code from the root using "*php src/index.php*"
2. Logged data is in log/coding-test.log. If this file hasn't been created check the directory permissions

**Testing**
1. Run the tests from the test directory using "*../vendor/bin/phpunit .*"

**Docker**
1. Build by running "*docker-compose build*" in the root directory
2. Start by running "*docker-compose up -d*" in the root directory
3. Use "*docker-compose build --no-cache*" to force a rebuild using pull of new code from git
4. The container is in tty mode so you can log in using "*docker exec -it veri_test bash*"
5. The working directory on the docker container is "*/etc/veri_coding_test*
6. You may then run unit tests or the code using the same methods as on localhost

**Output**
1. The required output is written to stdio
2. Log file data is saved to "*log/coding-test.log*". If the process does not have permission to access this directory, 
it carries on with no logging and no warning is given. However, a unit test failure will be generated if the log file 
can't be written

