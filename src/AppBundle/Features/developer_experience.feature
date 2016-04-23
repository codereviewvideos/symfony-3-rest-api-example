Feature: To improve the developer experience of our API

  In order to offer an API user a better experience
  As an API developer
  I need to return useful information when situations may be otherwise confusing


  Background:
    Given there are Users with the following details:
      | uid | username | email          | password |
      | u1  | peter    | peter@test.com | testpass |
      | u2  | john     | john@test.org  | johnpass |
    And I am successfully logged in with username: "peter", and password: "testpass"
    And when consuming the endpoint I use the "headers/content-type" of "application/json"


  Scenario: User must have the right Content-type
    When I have forgotten to set the "headers/content-type"
     And I send a "PATCH" request to "/users/u1"
    Then the response code should be 415
     And the response should contain json:
      """
      {
        "code": 415,
        "message": "Invalid or missing Content-type header"
      }
      """