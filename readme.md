**Assumptions**
1. Currency is in euro (or something with units and cents). This assumption is used to justify a crude representation 
of the payment as a float value rounded to 2 decimal places. This would need to be fixed for a production implementation
2. All dates and times are in the timezone of the machine on which the code is running. This could lead to some payment
errors on the users birthday if the DOB is in a different timezone.
