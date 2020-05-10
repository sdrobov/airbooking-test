# Test project using symfony 5

This is a test project that I did as a test task for a company.

# Installation
- Clone this repo
- `composer install`
- `make start`
Now web server is listening on https://localhost
- `make php`, then in new shell `./bin/console d:s:c`, then `./bin/console d:f:l`
- You're great :)

# API
- POST /api/v1/login_check sample payload: `{"username":"test","password":"qwerty"}`
- GET /api/v1/flights
- GET /api/v1/flight/{flightId}/seats
- POST /api/v1/flight/{flightId}/seat/{seatNum}/book
- POST /api/v1/flight/{flightId}/seat/{seatNum}/buy
- DELETE /api/v1/flight/{flightId}/seat/{seatNum}/book
- DELETE /api/v1/flight/{flightId}/seat/{seatNum}/buy
- POST /api/v1/callback/events sample payload: `{"data":{"flight_id":1,"triggered_at":1585012345,"event":"flight_ticket_sales_completed","secret_key":"a1b2c3d4e5f6a1b2c3d4e5f6"}}`
