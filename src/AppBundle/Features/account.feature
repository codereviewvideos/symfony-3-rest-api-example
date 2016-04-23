Feature: Manage Account data through API

  In order to offer a User access to their Account resource via an hypermedia API
  As a client software developer
  I need to be able to retrieve, create, update and delete JSON encoded Account resources.


  Background:
    Given there are Users with the following details:
      | uid  | username | email          | password |
      | u1   | peter    | peter@test.com | testpass |
      | u2   | john     | john@test.org  | johnpass |
      | u3   | dave     | dave@test.net  | davepass |
    And there are Accounts with the following details:
      | uid | name              | users |
      | a1  | account1          | u1    |
      | a2  | test account      | u2,u1 |
      | a3  | an empty account  |       |
    And I am successfully logged in with username: "peter", and password: "testpass"
    And when consuming the endpoint I use the "headers/content-type" of "application/json"


  Scenario: User can GET a Collection of their Account objects
    When I send a "GET" request to "/accounts"
    Then the response code should be 200
    And the response header "Content-Type" should be equal to "application/json; charset=utf-8"
    And the response should contain json:
      """
      [{
          "id": "a1",
          "name": "account1",
          "users": [{
              "id": "u1",
              "username": "peter",
              "email": "peter@test.com"
          }]
      }, {
          "id": "a2",
          "name": "test account",
          "users": [{
              "id": "u1",
              "username": "peter",
              "email": "peter@test.com"
            }, {
              "id": "u2",
              "username": "john",
              "email": "john@test.org"
          }]
      }]
      """


  Scenario: User can GET an individual Account by ID
    When I send a "GET" request to "/accounts/a1"
    Then the response code should be 200
    And the response header "Content-Type" should be equal to "application/json; charset=utf-8"
    And the response should contain json:
        """
        {
          "id": "a1",
          "name": "account1",
          "users": [{
              "id": "u1",
              "username": "peter",
              "email": "peter@test.com"
          }]
        }
        """


  Scenario: User cannot determine if another Account is active
    When I send a "GET" request to "/accounts/a3"
    Then the response code should be 403


  Scenario: User can add a new Account
    When I send a "POST" request to "/accounts" with body:
        """
        {
          "name": "a new account name",
          "users": [{
              "id": "u1",
              "username": "peter",
              "email": "peter@test.com"
          }]
        }
        """
    Then the response code should be 201
    And the response header "Content-Type" should be equal to "application/json; charset=utf-8"
    And the I follow the link in the Location response header
    And the response should contain json:
        """
        {
          "name": "a new account name",
          "users": [{
              "id": "u1",
              "username": "peter",
              "email": "peter@test.com"
          }]
        }
        """

@t
  Scenario: User can PATCH to update their data
    When I send a "PATCH" request to "/accounts/a1" with body:
        """
        {
          "name": "an updated account name"
        }
        """
    Then the response code should be 204
    And I send a "GET" request to "/accounts/a1"
    And the response should contain json:
        """
        {
            "id": "a1",
            "name": "an updated account name",
            "users": [{
              "id": "u1",
              "username": "peter",
              "email": "peter@test.com"
            }]
        }
        """

  @f
  Scenario: User can PATCH to re-assign an Account to another User
    When I send a "PATCH" request to "/accounts/a1" with body:
        """
        {
          "users": [{"id":"u3"}]
        }
        """
    Then the response code should be 204
    And the I follow the link in the Location response header
    Then the response code should be 403


  Scenario: User cannot PATCH to assign an Account to no one
    When I send a "PATCH" request to "/accounts/a1" with body:
        """
        {
          "name": "some account",
          "users": []
        }
        """
    Then the response code should be 400


  Scenario: User cannot PATCH to assign an Account to an invalid User
    When I send a "PATCH" request to "/accounts/a1" with body:
        """
        {
          "name": "some account",
          "users": [{"id":"u1"},{"id":"bad-id"}]
        }
        """
    Then the response code should be 403


  Scenario: User cannot PATCH to update an Account with which they are not associated
    When I send a "PATCH" request to "/accounts/a3"
    Then the response code should be 403


  Scenario: User cannot PATCH to update a none-existent Account
    When I send a "PATCH" request to "/accounts/madeup"
    Then the response code should be 403

  @f
  Scenario: User can PUT to replace their Account data
    When I send a "PUT" request to "/accounts/a1" with body:
        """
        {
          "name": "a replaced account name",
          "users": [{
              "id": "u1",
              "username": "peter",
              "email": "peter@test.com"
          }, {
              "id": "u2",
              "username": "john",
              "email": "john@test.org"
          }]
        }
        """
    Then the response code should be 204
    And the I follow the link in the Location response header
    And the response should contain json:
        """
        {
            "id": "a1",
            "name": "a replaced account name",
            "users": [{
                "id": "u1",
                "username": "peter",
                "email": "peter@test.com"
            }, {
                "id": "u2",
                "username": "john",
                "email": "john@test.org"
            }]
        }
        """

  @f @refactor-to-account-manager
  Scenario: User can PUT to re-assign an Account to another User
    When I send a "PUT" request to "/accounts/a1" with body:
        """
        {
          "name": "some account",
          "users": [{"id":"u2"}]
        }
        """
    Then the response code should be 204
    And the I follow the link in the Location response header
    Then the response code should be 403


  Scenario: User cannot PUT to assign an Account to no one
    When I send a "PUT" request to "/accounts/a1" with body:
        """
        {
          "name": "some account",
          "users": []
        }
        """
    Then the response code should be 400


  Scenario: User cannot PUT to assign an Account to an invalid User
    When I send a "PUT" request to "/accounts/a1" with body:
        """
        {
          "name": "some account",
          "users": [{"id":"u1"},{"id":"bad-id"}]
        }
        """
    Then the response code should be 403


  Scenario: User cannot PUT to update another User's Account
    When I send a "PUT" request to "/accounts/a3"
    Then the response code should be 403


  Scenario: User cannot PUT to update an Account that doesn't exist
    When I send a "PUT" request to "/accounts/bad-account-id"
    Then the response code should be 403


  Scenario: User can DELETE an Account they own
    When I send a "DELETE" request to "/accounts/a1"
    Then the response code should be 204
    And the "Account" with id: a1 should have been deleted


  Scenario: User cannot DELETE an Account they do not own
    When I send a "DELETE" request to "/accounts/a3"
    Then the response code should be 403