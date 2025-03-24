Setup Instruction

1. env setup

create a .env file and add the following details

app.baseURL = base url according to your file path

database.default.hostname = 
database.default.database = call_alert_system
database.default.username = 
database.default.password = 
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

GOOGLE_CLIENT_ID= your google client ID
GOOGLE_CLIENT_SECRET= client secret
GOOGLE_REDIRECT_URI= callback url like "yourbaseurl/auth/callback"

TWILIO_SID= your twilio SID
TWILIO_AUTH_TOKEN= Auth token
TWILIO_PHONE_NUMBER= twilio phone number


2. crone setup

create a crone to run with URL "yourbaseurl/twilio/call"

3. databse file

you can find the databse file in db folder on the root directory named "call_alert_system"


