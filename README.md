# task01

## infastructure deployment

### deploy the stack
```
aws cloudformation create-stack --stack-name project01 --template-body file://project01-template.yaml --region eu-central-1 --capabilities CAPABILITY_IAM CAPABILITY_NAMED_IAM
```

## populate data into mariadb instance
mysql/mariadb client and aws-cli needs to be installed for this to work

```
mysql --user=taskadmin01 --password=Ni8Q8rbv2cR35tf --host=$(aws rds describe-db-instances --db-instance-identifier mariadb01 --region eu-central-1 --query 'DBInstances[0].Endpoint.Address' --output text) <sql-data.sql
```

```
mariadb --user=taskadmin01 --password=Ni8Q8rbv2cR35tf --host=$(aws rds describe-db-instances --db-instance-identifier mariadb01 --region eu-central-1 --query 'DBInstances[0].Endpoint.Address' --output text) <sql-data.sql
```

## get the ip for the web page
```
aws cloudformation describe-stacks --stack-name mystack01 --region eu-central-1 --query 'Stacks[0].Outputs[?OutputKey==`webpageip`].OutputValue' --output text
```

## tests 
the test is headless browser that connects to the web page and compares the data on the page with the data in the database. If connection to/from the app or the test container to the db/app is not working the test will fail. Modifying the index to add/remove text/data would cause the tests to fail.
